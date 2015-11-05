[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/inviqa/magento-symfony-container/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/inviqa/magento-symfony-container/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/inviqa/magento-symfony-container/badges/build.png?b=master)](https://scrutinizer-ci.com/g/inviqa/magento-symfony-container/build-status/master)
# magento-symfony-container
Provides Magento with an instance of a Symfony DI Container

Documentation on the Symfony DI Component can be found [here](http://symfony.com/doc/current/components/dependency_injection/index.html).

Upon requesting the container for the first time, the configuration directories are scanned and the container compiled. If developer mode is off, the container will be cached in public/var/cache/container.cache.php and subsequently ready from there. To force the cache to refresh simply delete this file. If you want to container to be built for every request, make your sure switch on Magento developer-mode (in this state any existing container.cache.php file will be ignored).

## Services Configuration

All services configuration files are expected to be found in either the system-wide etc/ directory or within each modules etc/ directory. The default format is XML, therefore the configuration files are expected to be called "services.xml"

The following is an example of defining a service named "acme.checkout", which, in turn, depends on a Magento catalog model and a mail service. Via the configuration, we can provide "acme.product.catalog" - which is constructed by calling Mage::getModel('inviqa_acme/catalog') - as a dependency to "acme.checkout". Thus our "acme.checkout" service/class is now decoupled from Magento and its logic and business rules can be tested independently.

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        
        <service id="acme.checkout" class="Inviqa\Acme\Checkout">
            <argument type="service" id="acme.product.catalog" />
            <argument type="service" id="acme.mailer" />
        </service>
        
        <service id="acme.product.catalog" class="Inviqa_Acme_Model_Catalog">
            <factory class="Mage" method="getModel"/>
            <argument>inviqa_acme/catalog</argument>
        </service>
        
        <service id="acme.mailer" class="Inviqa\Mailer">
        </service>
        
    </services>
</container>
```

Your implementation of Inviqa\Acme\Checkout might look something like this:
```php
namespace Inviqa\Acme;

use Inviqa\Acme\Catalog;
use Inviqa\Mailer;

class Checkout
{
    private $catalog;
    
    private $mailer;

    public function __construct(Catalog $catalog, Mailer $mailer)
    {
        $this->catalog = $catalog;
        $this->mailer = $mailer;
    }
    
    public function process()
    {
        // checkout processing rules
    }
}
```

Along with a Catalog interface
```php
namespace Inviqa\Acme

interface Catalog
{
    public function process(Products $products = null);
    public function start();
}
```

And have your Inviqa_Acme_Model_Catalog implment the Catalog interface.

## Usage

The idea behind using a DI container is to be able to easily decouple your code, therefore direct access to the container is bad practice and should be limited to where absolutely necessary for example where a service cannot be provided automatically (e.g. a Magento controller or observer).

### Usage via trait
```php
class Inviqa_Acme_IndexController
{
    use Inviqa_SymfonyContainer_Helper_ServiceProvider;

    public function indexAction()
    {
        $this->getService('acme.checkout')->process();
        
        $this->loadLayout();
        $this->renderLayout();
    }
}
```

### Usage directly via helper


```php
$container = Mage::helper('inviqa_symfonyContainer/containerProvider')->getContainer();
```

### Test Environment
The configuration builder reads the Magento configuration node "global/environment" and uses this to switch the container generator to test environment. The string expected in "global/environment" node is "test". In this mode, the container generator will read additional services_test.xml files, which will override services defined in services.xml if their id's match. In this way, you can use "mock" services for integration testing purposes. (see additional documentation in https://github.com/inviqa/symfony-container-generator)

### Providing Magento Store Configuration Values to Service Constructors
If your service requires a value from the store configuration, something which would normally require calling Mage::getStoreConfig('web/secure/base_url') for example, you can use the special tag mage.config in place of an <argument> node in your service definition. The "key" attribute is a regular Magento store configuration key. This will simply add the value of requested store configuration to the list of service constructor arguments:

```xml
    <services>
        <service id="acme.service" class="Acme_Service">
            <argument type="service" id="some.service"/>
            <tag name="mage.config" key="payment/amazonpayments_cba/title"/>
            <tag name="mage.config" key="web/secure/base_url"/>
        </service>
    </services>
```

This will result in the constructor of Acme_Service being called with two extra arguments:

```php
class Acme_Service
{
    public function __construct(SomeSerivce $someService, $amazonPaymentsTitle, $secureBaseUrl)
    {
        $this->_secureBaseUrl = $secureBaseUrl; // will have value of e.g: https://my-magento.dev/  
    }
}
```

If the specified key does not exist, or is empty, the argument will be null or empty.

### Explicitly Providing Dependencies via __dependencies (since version 0.5.0)

Certain Magento classes, such as controllers, observers and blocks are instantiated by the system and therefore cannot be instantiated by the DI Container, nevertheless an additional service tag can make it easier to explicitly provide dependencies to these classes. Adding the mage.injectable tag to a service will allow you to provide this services dependencies *after* the service has been instantiated. The services arguments will be provided to the __dependencies method provided you call ServiceInjector::setupDependencies($class) after service instantiation, and your service has a __dependencies method that contains typehints that match the arguments type an order.

For convenience, setupDependencies is called on controllers by hooking into the pre-dispatch event, for other classes such as observers and blocks you will have to call it yourself by overriding the class constructor.

For controllers, a service definition might look like this
```xml
        <service id="acme.product.catalog" class="Mage_Catalog_Model_Product">
            <factory class="Mage" method="getModel"/>
            <argument>catalog/product</argument>
        </service>

        <service id="acme.sales.order" class="Mage_Sales_Model_Order">
            <factory class="Mage" method="getModel"/>
            <argument>sales/order</argument>
        </service>

        <service id="controllers.acme" class="Acme_Shop_IndexController">
            <argument type="service" id="acme.product.catalog"/>
            <argument type="service" id="acme.sales.order"/>
            <tag name="mage.injectable"/>
        </service>
```

And the class itself will implement __dependencies() thus:
```php
    /**
     * @var Mage_Catalog_Model_Product
     */
    private $catalog;

    /**
     * @var Mage_Sales_Model_Order
     */
    private $order;

    /**
     * @param Mage_Catalog_Model_Product $catalog
     * @param Mage_Sales_Model_Order $order
     */
    public function __dependencies(
        Mage_Catalog_Model_Product $catalog,
        Mage_Sales_Model_Order $order
    ) {
        $this->catalog = $catalog;
        $this->order = $order;
    }
```

Unfortunately your controller will still be coupled to Mage due to extending Mage_Core_Controller_Front_Action, and thus - untestable as a unit, but it will be clear what its dependencies are and they will be type-hinted.

To provide dependencies to other classes after they are instantiated, in addition to using the mage.injectable tag and implementing __dependencies, you will have to override your class' constructor, for example:
```php
    function __construct()
    {
        Mage::getSingleton('inviqa_symfonyContainer/serviceInjector')->setupDepdendencies($this);
        parent::__construct();
    }
```

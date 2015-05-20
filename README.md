# magento-symfony-container
Provides Magento with an instance of a Symfony DI Container

For documentation on the Symfony DI Componenent here [here](http://symfony.com/doc/current/components/dependency_injection/index.html)

Upon requesting the container for the first time, the configuration diretories are scanned and the container compiled. If developer mode is off, the container will be cached in public/var/cache/container.cache.php and subsequently ready from there. To force the cache to refresh simply delete this file. If you want to container to be built for every request, make your sure switch on Magento developer-mode (in this state any existing container.cache.php file will be ignored).

## Services Configuration

All services configuration files are expexted to be found in either the system-wide etc/ directory or withing each modules etc/ directory. The default format is XML, therefore the configuration files are expected to be called "services.xml"

The following is an example of defining a service named "acme.checkout", which, in turn, depends on a magento catalog model and a mail service. Via the configuration, we can provide "acme.product.catalog" - which is constructed by calling Mage::getModel('inviqa_acme/catalog') - as a dependency to "acme.checkout". Thus our "acme.checkout" service/class is now decoupled from Magento and its logic and business rules can be tested independently.

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
            <argument>inviqa_came/catalog</argument>
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

The idea behind using a DI container is to be able to easily decouple your code, therefore direct access to the container is bad practice and should be limited to where absolutely neccessary for example where a service cannot be provided automatically (e.g. a Magento controller or observer).

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

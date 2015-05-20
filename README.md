# magento-symfony-container
Provides Magento with an instance of a Symfony DI Container

For documentation on the Symfony DI Componenent see here [here](http://symfony.com/doc/current/components/dependency_injection/index.html)

All services configuration files are expexted to be found in either the system-wide etc/ directory or withing each modules etc/ directory. The default format is XML, therefore the configuration files are expected to be called "services.xml"

## Services Configuration

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
```
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

## Usage in Controller
```php
class Inviqa_Acme_IndexController extends Inviqa_SymfonyContainer_Controller_Base
{
    public function indexAction()
    {
        $this->_controller->get('acme.chekcout')->start();
        
        $this->loadLayout();
        $this->renderLayout();
    }
}
```

## Usage in Observer
```php
class Inviqa_Acme_Model_Observer extends Inviqa_SymfonyContainer_Model_Observer
{
    public function postCheckout(Products $products)
    {
        $this->_controller->get('acme.checkout')->process($products);
    }
}
```

## Access to container anywhere else

The idea behind using a DI container is to be able to easily decouple your code, therefore direct access to the container is bad practice and should be limited to where absolutely neccessary for example where a service cannot be provided automatically (e.g. a Magento controller or observer).

```php
$container = Mage::helper('inviqa_symfonyContainer/containerProvider')->getContainer();
```

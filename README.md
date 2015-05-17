# magento-symfony-container
Provides Magento with an instance of a Symfony DI Container

## Usage in Controller
```php
class Inviqa_Zygourator_IndexController extends Inviqa_SymfonyContainer_Controller_Base
{
    public function indexAction()
    {
        $this->_controller->get('acme.mailer')->send();
        
        $this->loadLayout();
        $this->renderLayout();
    }
}
```

## Usage in Observer
```php
class Inviqa_Zygourator_Model_Observer extends Inviqa_SymfonyContainer_Model_Observer
{
    public function productSaved()
    {
        $this->_controller->get('acme.mailer')->send();
    }
}
```

## Access to container anywhere else
```php
$container = Mage::getSingleton'inviqa_symfonyContainer/config')->getContainer();
```

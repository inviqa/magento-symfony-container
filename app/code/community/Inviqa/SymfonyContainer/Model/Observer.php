<?php
use Inviqa_SymfonyContainer_Model_InjectableCompilerPass as InjectableCompilerPass;

class Inviqa_SymfonyContainer_Model_Observer
{
    use Inviqa_SymfonyContainer_Helper_ServiceProvider;

    public function onCacheRefresh(Varien_Event_Observer $event)
    {
        if (Inviqa_SymfonyContainer_Model_ConfigurationBuilder::MODEL_ALIAS === $event->getType()) {
            unlink(Mage::getBaseDir('cache') . '/' . Inviqa_SymfonyContainer_Model_ConfigurationBuilder::CACHED_CONTAINER);
        }
    }
    public function onPreDispatch(Varien_Event_Observer $event)
    {
        $controller = $event->getControllerAction();
        $controllerServices = $this->getService(InjectableCompilerPass::INJECTABLES_SERVICE_ID)->controllers;

        if (!isset($controllerServices[get_class($controller)])) {
            return;
        }

        if (!in_array('setDependencies', get_class_methods($controller))) {
            return;
        }

        $services = $this->getServices($controllerServices[get_class($controller)]);

        call_user_func_array([$controller, 'setDependencies'], $services);
    }

    /**
     * @return array
     */
    private function getServices(array $serviceDefinitions)
    {
        return array_map(function($definition) { return $this->getService($definition); }, $serviceDefinitions);
    }

}

<?php

use Inviqa_SymfonyContainer_Model_InjectableCompilerPass as InjectableCompilerPass;

class Inviqa_SymfonyContainer_Helper_ServiceInjector
{
    use Inviqa_SymfonyContainer_Helper_ServiceProvider;

    public function inject($class)
    {
        $controllerServices = $this->getService(InjectableCompilerPass::INJECTABLES_SERVICE_ID)->controllers;

        if (!isset($controllerServices[get_class($class)])) {
            return;
        }

        if (!in_array('setDependencies', get_class_methods($class))) {
            return;
        }

        $services = $this->getServices($controllerServices[get_class($class)]);

        call_user_func_array([$class, 'setDependencies'], $services);
    }

    /**
     * @return array
     */
    private function getServices(array $serviceDefinitions)
    {
        return array_map(function($definition) { return $this->getService($definition); }, $serviceDefinitions);
    }
}
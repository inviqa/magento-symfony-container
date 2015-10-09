<?php

use Symfony\Component\DependencyInjection\Container;
use Inviqa_SymfonyContainer_Model_InjectableCompilerPass as InjectableCompilerPass;

class Inviqa_SymfonyContainer_Model_ServiceInjector
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param array $services
     */
    public function __construct(array $services)
    {
        $this->container = $services['container'];
    }

    /**
     * @param $class
     */
    public function setupDependencies($class)
    {
        $references = $this->container->get(InjectableCompilerPass::INJECTABLES_SERVICE_ID)->references;

        if (!isset($references[get_class($class)])) {
            return;
        }

        if (!in_array('__dependencies', get_class_methods($class))) {
            return;
        }

        $services = $this->getServices($references[get_class($class)]);

        call_user_func_array([$class, '__dependencies'], $services);
    }

    /**
     * @return array
     */
    private function getServices(array $serviceDefinitions)
    {
        return array_map(function($definition) { return $this->container->get($definition); }, $serviceDefinitions);
    }
}

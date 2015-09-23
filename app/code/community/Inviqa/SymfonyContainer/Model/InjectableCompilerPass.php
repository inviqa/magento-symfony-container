<?php

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class Inviqa_SymfonyContainer_Model_InjectableCompilerPass implements CompilerPassInterface
{
    const TAG_NAME = 'mage.injectable';

    const INJECTABLES_SERVICE_ID = 'mage.injectables';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            self::TAG_NAME
        );

        $controllersObject = (object) array(
            self::INJECTABLES_SERVICE_ID => array()
        );

        foreach ($taggedServices as $id => $tag) {

            $this->addDefinitionArguments($container->findDefinition($id), $controllersObject);
        }

        $container->set(self::INJECTABLES_SERVICE_ID, $controllersObject);
    }

    /**
     * @param $definition
     * @param $controllersObject
     */
    private function addDefinitionArguments(Definition $definition, stdClass $controllersObject)
    {
        $controllersObject->controllers[$definition->getClass()] = array();

        for ($arg = 0; $arg < count($definition->getArguments()); $arg++) {
            $controllersObject->controllers[$definition->getClass()][$arg] = $definition->getArgument($arg);
        }
    }
}

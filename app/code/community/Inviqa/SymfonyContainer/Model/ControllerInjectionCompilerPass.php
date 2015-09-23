<?php

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class Inviqa_SymfonyContainer_Model_ControllerInjectionCompilerPass implements CompilerPassInterface
{
    const TAG_NAME = 'mage.controller';

    const CONTROLLERS_SERVICE_ID = 'controllers';

    /**
     * Adds requested store configuration as an argument to the services
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            self::TAG_NAME
        );

        $controllersObject = (object) array(
            self::CONTROLLERS_SERVICE_ID => array()
        );

        foreach ($taggedServices as $id => $tag) {

            $definition = $container->findDefinition($id);

            $controllersObject->controllers[$definition->getClass()] = array();
            for ($arg=0; $arg < count($definition->getArguments()); $arg++) {
                $controllersObject->controllers[$definition->getClass()][$arg] =  $definition->getArgument($arg);
            }
        }

        $container->set(self::CONTROLLERS_SERVICE_ID, $controllersObject);
    }
}

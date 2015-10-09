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

        $dependables = (object) array(
            'references' => array()
        );

        foreach ($taggedServices as $id => $tag) {

            $this->addDefinitionArguments($container->findDefinition($id), $dependables);
        }

        $container->set(self::INJECTABLES_SERVICE_ID, $dependables);
    }

    /**
     * @param Definition $definition
     * @param stdClass $dependables
     */
    private function addDefinitionArguments(Definition $definition, stdClass $dependables)
    {
        $dependables->references[$definition->getClass()] = array();

        for ($arg = 0; $arg < count($definition->getArguments()); $arg++) {
            $dependables->references[$definition->getClass()][$arg] = $definition->getArgument($arg);
        }
    }
}

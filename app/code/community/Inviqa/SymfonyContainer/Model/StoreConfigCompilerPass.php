<?php

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Inviqa_SymfonyContainer_Model_StoreConfigCompilerPass implements CompilerPassInterface
{
    /**
     * Adds requested store configuration as an argument to the services
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'mage.config'
        );

        foreach ($taggedServices as $id => $tag) {
            $definition = $container->findDefinition($id);

            foreach ($tag as $attribute) {
                if (isset($attribute['key'])) {
                    $configValue = Mage::getStoreConfig($attribute['key']);
                    $definition->addArgument($configValue);
                }
            }
        }
    }
}
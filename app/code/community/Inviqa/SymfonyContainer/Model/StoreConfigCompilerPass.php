<?php

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Inviqa_SymfonyContainer_Model_StoreConfigCompilerPass implements CompilerPassInterface
{
    const TAG_NAME = 'mage.config';

    /**
     * @var Mage_Core_Model_App
     */
    private $_app;

    public function __construct(array $services = array())
    {
        $this->_app = isset($services['app']) ? $services['app'] : Mage::app();
    }

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

        foreach ($taggedServices as $id => $tag) {
            $definition = $container->findDefinition($id);

            foreach ($tag as $attribute) {
                if (isset($attribute['key'])) {
                    $configValue = $this->_app->getStore()->getConfig($attribute['key']);
                    $definition->addArgument($configValue);
                }
            }
        }
    }
}

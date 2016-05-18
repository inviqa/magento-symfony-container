<?php

use ContainerTools\Configuration;
use ContainerTools\ContainerGenerator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Bridge\MageApp;

class Inviqa_SymfonyContainer_Helper_ContainerProvider
{
    const HELPER_NAME = 'inviqa_symfonyContainer/containerProvider';

    /**
     * @var Mage_Core_Model_App
     */
    private $_mageApp;

    /**
     * @var Container
     */
    private $_container;

    /**
     * @var Configuration
     */
    private $_generatorConfig;

    /**
     * @var CompilerPassInterface
     */
    private $_storeConfigCompilerPass;
    /**
     * @var CompilerPassInterface
     */
    private $_injectableCompilerPass;

    public function __construct(array $services = array())
    {
        $this->_mageApp = isset($services['app']) ? $services['app'] : Mage::app();

        $this->_generatorConfig = isset($services['generatorConfig']) ?
            $services['generatorConfig'] :
            Mage::getModel('inviqa_symfonyContainer/configurationBuilder')->build();

        $this->_storeConfigCompilerPass = isset($services['storeConfigCompilerPass']) ?
            $services['storeConfigCompilerPass'] :
            Mage::getModel('inviqa_symfonyContainer/storeConfigCompilerPass');

        $this->_injectableCompilerPass = isset($services['injectableCompilerPass']) ?
            $services['injectableCompilerPass'] :
            Mage::getModel('inviqa_symfonyContainer/injectableCompilerPass');
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->_container ?: $this->_buildContainer();
    }

    /**
     * @return Container
     */
    private function _buildContainer()
    {
        $this->_generatorConfig->addCompilerPass($this->_storeConfigCompilerPass);
        $this->_generatorConfig->addCompilerPass($this->_injectableCompilerPass);

        $this->_mageApp->dispatchEvent(
            'symfony_container_before_container_generator',
            ['generator_config' => $this->_generatorConfig]
        );

        $generator = new ContainerGenerator($this->_generatorConfig);

        return $this->_container = $generator->getContainer();
    }
}

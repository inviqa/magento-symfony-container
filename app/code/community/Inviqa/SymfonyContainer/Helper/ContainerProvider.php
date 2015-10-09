<?php

use ContainerTools\Configuration;
use ContainerTools\ContainerGenerator;
use Symfony\Component\DependencyInjection\Container;

class Inviqa_SymfonyContainer_Helper_ContainerProvider
{
    const HELPER_NAME = 'inviqa_symfonyContainer/containerProvider';

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

        $generator = new ContainerGenerator($this->_generatorConfig);

        return $this->_container = $generator->getContainer();
    }
}

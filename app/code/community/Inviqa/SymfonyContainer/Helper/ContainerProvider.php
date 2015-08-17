<?php

use ContainerTools\Configuration;
use ContainerTools\ContainerGenerator;
use Symfony\Component\DependencyInjection\Container;

class Inviqa_SymfonyContainer_Helper_ContainerProvider
{

    /**
     * @var Container
     */
    private $_container;

    /**
     * @var Configuration
     */
    private $_generatorConfig;


    public function __construct(array $services = array())
    {
        $this->_generatorConfig = isset($services['generatorConfig']) ?
            $services['generatorConfig'] :
            Mage::getModel('inviqa_symfonyContainer/configurationBuilder');
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
        $this->_generatorConfig->addCompilerPass(new Inviqa_SymfonyContainer_Model_ExampleCompilerPass());

        $generator = new ContainerGenerator($this->_generatorConfig);

        return $this->_container = $generator->getContainer();
    }
}

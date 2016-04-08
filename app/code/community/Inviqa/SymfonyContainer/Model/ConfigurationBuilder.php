<?php

use ContainerTools\Configuration;

class Inviqa_SymfonyContainer_Model_ConfigurationBuilder
{
    const MODEL_ALIAS = 'inviqa_symfonyContainer';
    const CACHED_CONTAINER = 'container.cache.php';
    const ENVIRONMENT_NODE = 'global/environment';
    const TEST_ENVIRONMENT = 'test';

    /**
     * @var Mage_Core_Model_App
     */
    private $_mageApp;

    /**
     * @var string
     */
    private $_baseDir;

    /**
     * @var Mage_Core_Model_Config
     */
    private $_config;

    /**
     * @param array $services Awkward way of passing deps to spec mage object
     */
    public function __construct(array $services = array())
    {
        $this->_mageApp = isset($services['app']) ? $services['app'] : Mage::app();
        $this->_baseDir = isset($services['baseDir']) ? $services['baseDir'] : Mage::getBaseDir('cache');
        $this->_config = isset($services['config']) ? $services['config'] : Mage::getConfig();
    }

    /**
     * @return Configuration
     */
    public function build()
    {
        $servicesFormat = 'xml';
        $cachedContainer = $this->_baseDir . '/' . self::CACHED_CONTAINER;
        $configuration = Configuration::fromParameters(
            $cachedContainer,
            $this->_collectConfigFolders(),
            !$this->_mageApp->useCache(self::MODEL_ALIAS),
            $servicesFormat
        );

        $configuration->setTestEnvironment($this->_isTestEnvironment());

        return $configuration;
    }

    /**
     * @return array List of all "etc" folders
     */
    private function _collectConfigFolders()
    {
        $folders = array($this->_config->getOptions()->getEtcDir());

        return $this->_addModuleFolders($folders);
    }

    /**
     * @param array $folders
     *
     * @return array
     */
    private function _addModuleFolders($folders)
    {
        foreach ($this->_config->getNode('modules')->children() as $name => $module) {
            if ($module->active) {
                $folders[] = $this->_config->getModuleDir('etc', $name);
            }
        }

        return $folders;
    }

    /**
     * @return bool
     */
    private function _isTestEnvironment()
    {
        return (string) $this->_config->getNode(self::ENVIRONMENT_NODE) === self::TEST_ENVIRONMENT;
    }
}

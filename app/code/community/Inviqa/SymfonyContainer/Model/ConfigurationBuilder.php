<?php

use ContainerTools\Configuration;

class Inviqa_SymfonyContainer_Model_ConfigurationBuilder
{
    const MODEL_ALIAS = 'inviqa_symfonyContainer';

    const CACHED_CONTAINER = 'container.cache.php';

    /**
     * @param array $services Awkward way of passing deps to spec mage object
     */
    public function __construct(array $services = array())
    {
        $this->_app = isset($services['app']) ? $services['app'] : Mage::app();
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

        return Configuration::fromParameters(
            $cachedContainer,
            $this->_collectConfigFolders(),
            !$this->_app->useCache(self::MODEL_ALIAS),
            $servicesFormat
        );
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
}

<?php

use ContainerTools\Configuration;
use ContainerTools\ContainerGenerator;

class Inviqa_SymfonyContainer_Helper_ContainerProvider
{
    const MODEL_ALIAS = 'inviqa_symfonyContainer';

    const CACHED_CONTAINER = 'container.cache.php';

    /**
     * @var Container
     */
    private $_container;

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer()
    {
        return $this->_container ?: $this->_buildContainer();
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    private function _buildContainer()
    {
        $servicesFormat = 'xml';
        $cachedContainer = Mage::getBaseDir('cache') . '/' . self::CACHED_CONTAINER;
        $useCache = Mage::app()->useCache(self::MODEL_ALIAS);

        $configuration = Configuration::fromParameters($cachedContainer, $this->_collectConfigFolders(), !$useCache, $servicesFormat);

        $configuration->addCompilerPass(new Inviqa_SymfonyContainer_Model_ExampleCompilerPass());

        $generator = new ContainerGenerator($configuration);

        return $this->_container = $generator->getContainer();
    }

    /**
     * @return array List of all "etc" folders
     */
    private function _collectConfigFolders()
    {
        $mageConfig = Mage::getConfig();
        $folders = array($mageConfig->getOptions()->getEtcDir());

        foreach ($mageConfig->getNode('modules')->children() as $name => $module) {
            if ($module->active) {
                $folders[] = $mageConfig->getModuleDir('etc', $name);
            }
        }

        return $folders;
    }
}

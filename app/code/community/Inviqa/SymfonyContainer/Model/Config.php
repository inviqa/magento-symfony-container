<?php

use ContainerTools\ContainerGenerator;

class Inviqa_SymfonyContainer_Model_Config extends Mage_Core_Model_Config
{
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
        $format = 'xml';

        $generator = new ContainerGenerator(
            Mage::getBaseDir('cache') . '/container.cache.php',
            $this->_collectConfigFolders(),
            Mage::getIsDeveloperMode(),
            $format
        );

        return $this->_container = $generator->getContainer();
    }

    /**
     * @return array List of all "etc" folders
     */
    private function _collectConfigFolders()
    {
        $folders = array();
        $folders[] = $this->getOptions()->getEtcDir();

        foreach (Mage::getConfig()->getNode('modules')->children() as $name => $module) {
            if ($module->active) {
                $folders[] = Mage::getConfig()->getModuleDir('etc', $name);
            }
        }

        return $folders;
    }
}

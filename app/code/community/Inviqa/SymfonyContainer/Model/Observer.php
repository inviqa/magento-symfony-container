<?php

use Inviqa_SymfonyContainer_Model_ConfigurationBuilder as ConfigurationBuilder;
use Inviqa_SymfonyContainer_Helper_ContainerProvider as ContainerProvider;

class Inviqa_SymfonyContainer_Model_Observer
{
    use Inviqa_SymfonyContainer_Helper_ServiceProvider;

    const CACHE_META_SUFFIX = '.meta';
    const SERVICE_INJECTOR = 'inviqa_symfonyContainer/serviceInjector';

    public function onCacheRefresh(Varien_Event_Observer $event)
    {
        if ($event->getType() === ConfigurationBuilder::MODEL_ALIAS) {
            $this->clearCache();
        }
    }

    public function onCacheFlush()
    {
        $this->clearCache();
    }

    public function onPreDispatch(Varien_Event_Observer $event)
    {
        $controller = $event->getControllerAction();

        Mage::getSingleton(self::SERVICE_INJECTOR, [
            'container' => Mage::helper(ContainerProvider::HELPER_NAME)->getContainer()
        ])->setupDependencies($controller);
    }

    private function clearCache()
    {
        $containerFilePath = $this->containerCachePath();
        $metaFilePath = $this->containerCacheMetaPath();

        if (file_exists($containerFilePath)) {
            unlink($containerFilePath);
        }

        if (file_exists($metaFilePath)) {
            unlink($metaFilePath);
        }
    }

    private function containerCachePath()
    {
        return Mage::getBaseDir('cache') . DIRECTORY_SEPARATOR . ConfigurationBuilder::CACHED_CONTAINER;
    }

    private function containerCacheMetaPath()
    {
        return $this->containerCachePath() . self::CACHE_META_SUFFIX;
    }
}

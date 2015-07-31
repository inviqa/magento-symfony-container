<?php


class Inviqa_SymfonyContainer_Model_Observer
{
    public function onCacheRefresh(Varien_Event_Observer $event)
    {
        if (Inviqa_SymfonyContainer_Helper_ContainerProvider::MODEL_ALIAS === $event->getType()) {
            unlink(Mage::getBaseDir('cache') . '/' . Inviqa_SymfonyContainer_Helper_ContainerProvider::CACHED_CONTAINER);
        }
    }
}
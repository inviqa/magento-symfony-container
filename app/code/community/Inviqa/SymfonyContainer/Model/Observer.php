<?php

class Inviqa_SymfonyContainer_Model_Observer
{
    public function onCacheRefresh(Varien_Event_Observer $event)
    {
        if (Inviqa_SymfonyContainer_Model_ConfigurationBuilder::MODEL_ALIAS === $event->getType()) {
            unlink(Mage::getBaseDir('cache') . '/' . Inviqa_SymfonyContainer_Model_ConfigurationBuilder::CACHED_CONTAINER);
        }
    }
    public function onPreDispatch(Varien_Event_Observer $event)
    {
        $controller = $event->getControllerAction();

        Mage::helper('inviqa_symfonyContainer/serviceInjector')->inject($controller);
    }
}

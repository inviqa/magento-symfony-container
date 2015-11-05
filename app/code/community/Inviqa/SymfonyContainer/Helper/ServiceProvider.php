<?php
use Inviqa_SymfonyContainer_Helper_ContainerProvider as ContainerProvider;

trait Inviqa_SymfonyContainer_Helper_ServiceProvider
{
    public function getService($serviceName)
    {
        return Mage::helper(ContainerProvider::HELPER_NAME)->getContainer()->get($serviceName);
    }
} 
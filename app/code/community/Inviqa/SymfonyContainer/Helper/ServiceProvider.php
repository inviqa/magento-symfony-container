<?php

trait Inviqa_SymfonyContainer_Helper_ServiceProvider
{
    public function getService($serviceName)
    {
        return Mage::helper('inviqa_symfonyContainer/containerProvider')->getContainer()->get($serviceName);
    }
} 
<?php


class Inviqa_SymfonyContainer_Model_Observer 
{
    protected $_container;

    public function __construct()
    {
        $this->_container = Mage::helper('inviqa_symfonyContainer/containerProvider')->getContainer();

        parent::__construct();
    }
} 
<?php


class Inviqa_SymfonyContainer_Model_Observer 
{
    protected $_container;

    public function _construct()
    {
        $this->_container = Mage::getSingleton('inviqa_symfonyContainer/config')->getContainer();

        parent::_construct();
    }
} 
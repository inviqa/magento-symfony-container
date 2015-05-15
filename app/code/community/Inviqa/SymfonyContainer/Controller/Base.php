<?php

class Inviqa_SymfonyContainer_Controller_Base extends Mage_Core_Controller_Front_Action
{
    protected $_container;

    public function _construct()
    {
        $this->_container = Mage::getModel('inviqa_symfonyContainer/config')->getContainer();

        parent::_construct();
    }
} 
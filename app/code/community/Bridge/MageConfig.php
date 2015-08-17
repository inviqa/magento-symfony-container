<?php
namespace Bridge;

interface MageConfig
{
    public function getOptions();

    public function getNode($name);

    public function getModuleDir($type, $name);
}

<?php

class Deploy_Instance
{
    const INSTANCE_TYPE_GIT      = 'git';
    const INSTANCE_TYPE_COMPOSER = 'composer';
    const INSTANCE_TYPE_INVALID  = 'invalid';

    private $instanceName;

    private $instancePath;

    private $instanceType;


    public function __construct($instanceName, $instancePath)
    {
        $this->instanceName = $instanceName;
        $this->instancePath = $instancePath;
    }

    public function getInstanceName()
    {
        return $this->instanceName;
    }

    public function getInstancePath()
    {
        return $this->instancePath;
    }

    public function getInstanceType()
    {
        if ($this->instanceType === null) {
            $this->instanceType = self::INSTANCE_TYPE_INVALID;
            if ($this->instancePath != '' && is_dir($this->instancePath)) {
                $indicator = 'vendor' . DS . 'magento' . DS . 'magento2-base';
                $this->instanceType = is_dir($this->instancePath . DS . $indicator)
                    ? self::INSTANCE_TYPE_COMPOSER
                    : self::INSTANCE_TYPE_GIT;
            }
        }

        return $this->instanceType;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Deploy;

class InstanceManager
{
    private $instanceList = [];


    public function __construct()
    {
        $this->generateInstanceList();
    }

    private function generateInstanceList()
    {
        $instanceListFilePath = BP . 'app/config/instance_list.ini';
        if (!file_exists($instanceListFilePath) || !is_readable($instanceListFilePath)) {
            return $this;
        }

        $declaredInstanceList = parse_ini_file($instanceListFilePath, true, INI_SCANNER_TYPED);
        foreach ($declaredInstanceList as $groupName => $groupInstanceList) {
            foreach ($groupInstanceList as $instanceName => $instancePath) {
                $this->instanceList[$groupName][] = is_int($instancePath)
                    ? $instancePath
                    : new Instance($instanceName, $instancePath);
            }
        }

        return $this;
    }

    public function getInstanceList()
    {
        return $this->instanceList;
    }
}

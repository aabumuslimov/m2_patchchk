<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch;

use Magento\PatchChecker\Deploy\Instance;
use Magento\PatchChecker\Deploy\InstanceManager;

/**
 * Abstract patch checker
 */
abstract class AbstractChecker
{
    const PATCH_APPLY_RESULT_FAILED     = 0;
    const PATCH_APPLY_RESULT_SUCCESSFUL = 1;
    const PATCH_APPLY_RESULT_MERGED     = 2;

    /**
     * @var InstanceManager
     */
    private $instanceManager;

    /**
     * @param InstanceManager $instanceManager
     */
    public function __construct(
        InstanceManager $instanceManager
    ) {
        $this->instanceManager = $instanceManager;
    }

    /**
     * Return patch status for each configured version.
     *
     * @param string $patch
     * @return array
     */
    public function check(string $patch)
    {
        $result = [];
        foreach ($this->instanceManager->getInstanceList() as $groupName => $groupInstanceList) {
            foreach ($groupInstanceList as $instance) {
                if (is_int($instance)) {
                    for ($i = 0; $i < $instance; $i++) {
                        $result[$groupName][] = ['instance_name' => 'n/a', 'result' => 'n/a'];
                    }
                } elseif ($instance->getInstanceType() == Instance::INSTANCE_TYPE_INVALID) {
                    $result[$groupName][] = ['instance_name' => $instance->getInstanceName(), 'result' => 'n/a'];
                } else {
                    $result[$groupName][] = [
                        'instance_name'  => $instance->getInstanceName(),
                        'result' => $this->getResult($instance, $patch)
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Get status of the patch
     *
     * @param Instance $instance
     * @param string $patch
     * @return array|string
     */
    abstract public function getResult(Instance $instance, string $patch);
}

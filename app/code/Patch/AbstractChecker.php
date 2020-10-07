<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch;

use Magento\PatchChecker\Deploy\Instance;
use Magento\PatchChecker\Deploy\InstanceManager;
use Magento\PatchChecker\Patch\Check\Strategy\StrategyInterface;
use Magento\PatchChecker\Patch\Check\StrategyManager;

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
     * @var StrategyManager
     */
    private $strategyManager;

    /**
     * @param InstanceManager $instanceManager
     * @param StrategyManager $strategyManager
     */
    public function __construct(
        InstanceManager $instanceManager,
        StrategyManager $strategyManager
    ) {
        $this->instanceManager = $instanceManager;
        $this->strategyManager = $strategyManager;
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
                        $result[$groupName][] = ['instance_name' => 'n/a', 'check_strategy' => 'n/a'];
                    }
                } else if ($instance->getInstanceType() == Instance::INSTANCE_TYPE_INVALID) {
                    $result[$groupName][] = ['instance_name' => $instance->getInstanceName(), 'check_strategy' => 'n/a'];
                } else {
                    $checkResult = [];
                    foreach ($this->strategyManager->getStrategyList() as $strategy) {
                        $strategyResult = $this->getResult($patch, $instance, $strategy);

                        if ($strategyResult == self::PATCH_APPLY_RESULT_MERGED) {
                            $checkResult = 'merged';
                            break;
                        }

                        $checkResult[$strategy->getStrategyName()] = $strategyResult;
                    }
                    $result[$groupName][] = [
                        'instance_name'  => $instance->getInstanceName(),
                        'check_strategy' => $checkResult
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Get status of the patch
     *
     * @param string $patch
     * @param Instance $instance
     * @param StrategyInterface $strategy
     * @return int
     */
    abstract public function getResult(string $patch, Instance $instance, StrategyInterface $strategy): int;
}

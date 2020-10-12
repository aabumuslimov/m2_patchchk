<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch;

use Magento\PatchChecker\Deploy\Instance;
use Magento\PatchChecker\Deploy\InstanceManager;
use Magento\PatchChecker\Patch\Check\StrategyManager;

/**
 * Patch checker
 */
class Checker extends AbstractChecker
{
    /**
     * @var InstancePatchConverter
     */
    private $patchConverter;
    /**
     * @var StrategyManager
     */
    private $strategyManager;

    /**
     * @param InstanceManager $instanceManager
     * @param StrategyManager $strategyManager
     * @param InstancePatchConverter $patchConverter
     */
    public function __construct(
        InstanceManager $instanceManager,
        StrategyManager $strategyManager,
        InstancePatchConverter $patchConverter
    ) {
        parent::__construct($instanceManager);
        $this->patchConverter = $patchConverter;
        $this->strategyManager = $strategyManager;
    }

    /**
     * @inheritDoc
     */
    public function getResult(Instance $instance, string $patch)
    {
        $patchForInstancePath = $this->patchConverter->convert($patch, $instance->getInstanceType());
        $checkResult = [];
        foreach ($this->strategyManager->getStrategyList() as $strategy) {
            $patchPath = ($strategy->getIsPreserveOriginalFileFormat())
                ? $patch
                : $patchForInstancePath;
            $strategyResult = $strategy->check($patchPath, $instance->getInstancePath());

            if ($strategyResult === self::PATCH_APPLY_RESULT_MERGED) {
                $checkResult = 'merged';
                break;
            }
            $checkResult[$strategy->getStrategyName()] = $strategyResult;
        }

        return $checkResult;
    }
}

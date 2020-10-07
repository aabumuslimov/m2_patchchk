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
 * Patch checker
 */
class Checker extends AbstractChecker
{
    /**
     * @var InstancePatchConverter
     */
    private $patchConverter;

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
        parent::__construct($instanceManager, $strategyManager);
        $this->patchConverter = $patchConverter;
    }

    /**
     * @inheritDoc
     */
    public function getResult(string $patch, Instance $instance, StrategyInterface $strategy): int
    {
        $patchForInstancePath = $this->patchConverter->convert($patch, $instance->getInstanceType());
        $patchPath = $strategy->getIsPreserveOriginalFileFormat()
            ? $patch
            : $patchForInstancePath;

        return $strategy->check($patchPath, $instance->getInstancePath());
    }
}

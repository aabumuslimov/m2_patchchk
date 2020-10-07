<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\Check\Strategy;

interface StrategyInterface
{
    public function getStrategyName();

    public function getIsPreserveOriginalFileFormat();

    public function check($patchPath, $instancePath);
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\Check;

use Magento\PatchChecker\Patch\Check\Strategy\GitApplyStrategy;
use Magento\PatchChecker\Patch\Check\Strategy\PatchStrategy;
use Magento\PatchChecker\Patch\Check\Strategy\StrategyInterface;

class StrategyManager
{
    private $strategyList = [];


    public function __construct()
    {
        $this->addStrategy(new PatchStrategy())
            ->addStrategy(new GitApplyStrategy());
    }

    public function addStrategy(StrategyInterface $strategy)
    {
        $this->strategyList[$strategy->getStrategyName()] = $strategy;
        return $this;
    }

    public function getStrategyList()
    {
        return $this->strategyList;
    }
}

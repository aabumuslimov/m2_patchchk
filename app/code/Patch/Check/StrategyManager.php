<?php

require_once 'app/code/Patch/Check/Strategy/StrategyInterface.php';
require_once 'app/code/Patch/Check/Strategy/AbstractStrategy.php';
require_once 'app/code/Patch/Check/Strategy/PatchStrategy.php';
require_once 'app/code/Patch/Check/Strategy/GitApplyStrategy.php';

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

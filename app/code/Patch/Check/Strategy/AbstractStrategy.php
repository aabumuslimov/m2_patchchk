<?php

abstract class AbstractStrategy implements StrategyInterface
{
    protected $strategyName;

    abstract protected function getCommand($patchPath, $instancePath);

    public function getStrategyName()
    {
        return $this->strategyName;
    }

    public function check($patchPath, $instancePath)
    {
        if (!$patchPath) {
            return false;
        }

        exec($this->getCommand($patchPath, $instancePath), $output, $returnStatus);
        return !$returnStatus;
    }
}

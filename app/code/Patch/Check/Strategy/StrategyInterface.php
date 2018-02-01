<?php

interface StrategyInterface
{
    public function getStrategyName();

    public function check($patchPath, $instancePath);
}

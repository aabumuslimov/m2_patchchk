<?php

interface StrategyInterface
{
    public function getStrategyName();

    public function getIsPreserveOriginalFileFormat();

    public function check($patchPath, $instancePath);
}

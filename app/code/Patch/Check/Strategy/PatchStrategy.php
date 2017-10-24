<?php

class PatchStrategy extends AbstractStrategy
{
    protected $strategyName = 'patch';

    protected function getCommand($patchPath, $instancePath)
    {
        return "patch --dry-run --directory=\"{$instancePath}\" -p0 < {$patchPath}";
    }
}

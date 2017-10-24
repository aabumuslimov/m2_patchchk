<?php

class GitApplyStrategy extends AbstractStrategy
{
    protected $strategyName = 'git_apply';

    protected function getCommand($patchPath, $instancePath)
    {
        // git apply can't check patch properly if --directory option is used with absolute path
        return "cd $instancePath \\ && git apply --check -p0 {$patchPath}";
    }
}

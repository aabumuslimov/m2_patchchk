<?php

class PatchStrategy extends AbstractStrategy
{
    protected $strategyName = 'patch';

    protected function getCommand($patchPath, $instancePath, $revertMode = false)
    {
        $options = [
            '--dry-run' => null,
            '--directory' => $instancePath,
            '-p0' => null
        ];

        if ($revertMode) {
            $options['-R'] = null;
        }

        return "patch {$this->renderOptions($options)} < {$patchPath}";
    }
}

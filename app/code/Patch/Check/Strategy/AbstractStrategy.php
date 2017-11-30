<?php

abstract class AbstractStrategy implements StrategyInterface
{
    protected $strategyName;

    abstract protected function getCommand($patchPath, $instancePath, $revertMode = false);

    protected function renderOptions($options)
    {
        $renderedOptions = '';
        foreach ($options as $option => $value) {
            $renderedOptions .= ' ' . $option;

            if ($value !== null) {
                $renderedOptions .= '="' . $value . '"';
            }
        }

        return $renderedOptions;
    }

    public function getStrategyName()
    {
        return $this->strategyName;
    }

    protected function executeCommand($command)
    {
        exec($command, $output, $returnStatus);
        return $returnStatus;
    }

    public function check($patchPath, $instancePath)
    {
        if (!$patchPath) {
            return false;
        }

        $result = $this->executeCommand($this->getCommand($patchPath, $instancePath));
        if (!$result) {
            return Patch_Checker::PATCH_APPLY_RESULT_SUCCESSFUL;
        }

        $result = $this->executeCommand($this->getCommand($patchPath, $instancePath, true));
        if (!$result) {
            return Patch_Checker::PATCH_APPLY_RESULT_MERGED;
        }
    }
}

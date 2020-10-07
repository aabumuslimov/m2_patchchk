<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\Check\Strategy;

use Magento\PatchChecker\Patch\Checker;

abstract class AbstractStrategy implements StrategyInterface
{
    protected $strategyName;

    /**
     * Defines if the patch file should be converted to some specific format before check
     * or the original format should be preserved.
     *
     * @var bool
     */
    protected $isPreserveOriginalFileFormat = false;

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

    public function getIsPreserveOriginalFileFormat()
    {
        return $this->isPreserveOriginalFileFormat;
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
            return Checker::PATCH_APPLY_RESULT_SUCCESSFUL;
        }

        $result = $this->executeCommand($this->getCommand($patchPath, $instancePath, true));
        if (!$result) {
            return Checker::PATCH_APPLY_RESULT_MERGED;
        }
    }
}

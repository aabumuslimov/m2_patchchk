<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\Check\Strategy;

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

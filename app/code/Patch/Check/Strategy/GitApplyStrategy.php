<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\Check\Strategy;

class GitApplyStrategy extends AbstractStrategy
{
    protected $strategyName = 'git_apply';

    /**
     * The validation is done using an original file to check strict apply format logic
     *
     * @var bool
     */
    protected $isPreserveOriginalFileFormat = true;

    protected function getCommand($patchPath, $instancePath, $revertMode = false)
    {
        // git apply can't check patch properly if --directory option is used with absolute path
        $options = ['--check' => null];

        if ($revertMode) {
            $options['-R'] = null;
        }

        return "cd {$instancePath} \\ && git apply {$this->renderOptions($options)} {$patchPath}";
    }
}

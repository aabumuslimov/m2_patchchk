<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch;

use Magento\PatchChecker\Deploy\Instance;
use Magento\PatchChecker\Deploy\InstanceManager;
use Magento\PatchChecker\Patch\Check\StrategyManager;

class Checker
{
    const PATCH_APPLY_RESULT_FAILED     = 0;
    const PATCH_APPLY_RESULT_SUCCESSFUL = 1;
    const PATCH_APPLY_RESULT_MERGED     = 2;


    private $instanceManager;

    private $strategyManager;

    private $patchConverter;

    private $originalPatchPath;

    private $patchPerInstanceType = [];


    public function __construct($patchPath)
    {
        $this->originalPatchPath    = $patchPath;
        $this->instanceManager      = new InstanceManager();
        $this->strategyManager      = new StrategyManager();
        $this->patchConverter       = new Converter();
    }

    private function getPatchForInstanceType($instanceType)
    {
        if (!isset($this->patchPerInstanceType[$instanceType])) {
            $patchPath = false;
            if ($instanceType == Instance::INSTANCE_TYPE_GIT) {
                $patchPath = BP . UPLOAD_PATH . pathinfo($this->originalPatchPath, PATHINFO_FILENAME) . '.git.patch';
                $isConverted = $this->patchConverter->convertFromComposerToGitFormat($this->originalPatchPath, $patchPath);
                if (!$isConverted) {
                    $patchPath = false;
                }
            } elseif ($instanceType == Instance::INSTANCE_TYPE_COMPOSER) {
                $patchPath = BP . UPLOAD_PATH . pathinfo($this->originalPatchPath, PATHINFO_FILENAME) . '.composer.patch';
                $isConverted = $this->patchConverter->convertFromGitToComposerFormat($this->originalPatchPath, $patchPath);
                if (!$isConverted) {
                    $patchPath = false;
                }
            }

            $this->patchPerInstanceType[$instanceType] = $patchPath;
        }

        return $this->patchPerInstanceType[$instanceType];
    }

    public function checkPatchForAllReleases()
    {
        $result = [];
        foreach ($this->instanceManager->getInstanceList() as $groupName => $groupInstanceList) {
            foreach ($groupInstanceList as $instance) {
                if (is_int($instance)) {
                    for ($i = 0; $i < $instance; $i++) {
                        $result[$groupName][] = ['instance_name' => 'n/a', 'check_strategy' => 'n/a'];
                    }
                    continue;
                }
                if ($instance->getInstanceType() == Instance::INSTANCE_TYPE_INVALID) {
                    $result[$groupName][] = ['instance_name' => $instance->getInstanceName(), 'check_strategy' => 'n/a'];
                    continue;
                }

                $patchForInstancePath = $this->getPatchForInstanceType($instance->getInstanceType());
                $checkResult = [];
                foreach ($this->strategyManager->getStrategyList() as $strategy) {
                    $patchPath = ($strategy->getIsPreserveOriginalFileFormat())
                        ? $this->originalPatchPath
                        : $patchForInstancePath;

                    $strategyResult = $strategy->check($patchPath, $instance->getInstancePath());

                    if ($strategyResult == self::PATCH_APPLY_RESULT_MERGED) {
                        $checkResult = 'merged';
                        break;
                    }

                    $checkResult[$strategy->getStrategyName()] = $strategyResult;
                }

                $result[$groupName][] = [
                    'instance_name'  => $instance->getInstanceName(),
                    'check_strategy' => $checkResult
                ];
            }
        }

        return $result;
    }
}

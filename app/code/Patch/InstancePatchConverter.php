<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch;

use Magento\PatchChecker\Deploy\Instance;

/**
 * Convert patch to git or composer format
 */
class InstancePatchConverter
{
    /**
     * @var Converter
     */
    private $patchConverter;

    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param Converter $patchConverter
     */
    public function __construct(Converter $patchConverter)
    {
        $this->patchConverter = $patchConverter;
    }

    /**
     * Convert patch to the format compatible with given instance type
     *
     * @param string $originalPatchPath
     * @param string $instanceType
     * @return false|string
     */
    public function convert(string $originalPatchPath, string $instanceType)
    {
        if (!isset($this->cache[$originalPatchPath]) || !isset($this->cache[$originalPatchPath][$instanceType])) {
            $patchPath = false;
            if ($instanceType === Instance::INSTANCE_TYPE_GIT) {
                $patchPath = BP . UPLOAD_PATH . pathinfo($originalPatchPath, PATHINFO_FILENAME) . '.git.patch';
                $isConverted = $this->patchConverter->convertFromComposerToGitFormat($originalPatchPath, $patchPath);
                if (!$isConverted) {
                    $patchPath = false;
                }
            } elseif ($instanceType === Instance::INSTANCE_TYPE_COMPOSER) {
                $patchPath = BP . UPLOAD_PATH . pathinfo($originalPatchPath, PATHINFO_FILENAME) . '.composer.patch';
                $isConverted = $this->patchConverter->convertFromGitToComposerFormat($originalPatchPath, $patchPath);
                if (!$isConverted) {
                    $patchPath = false;
                }
            }

            $this->cache[$originalPatchPath][$instanceType] = $patchPath;
        }

        return $this->cache[$originalPatchPath][$instanceType];
    }
}

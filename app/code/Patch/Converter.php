<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch;

use Magento\PatchChecker\File\Filesystem;

class Converter
{
    private $converterToolPath = 'app/bin/patch-converter.php';


    public function convert($sourcePath, $options, $destPath, $removePathPrefix = true)
    {
        exec("php {$this->converterToolPath} {$options} {$sourcePath} > {$destPath}", $output, $status);
        Filesystem::getInstance()->registerTmpFile($destPath);

        if (!$status && $removePathPrefix) {
            $status = !file_put_contents($destPath, $this->removePathPrefix(file_get_contents($destPath)));
        }

        return !$status;
    }

    public function removePathPrefix($content)
    {
        return preg_replace('~(\s+)(a/|b/)([^\s]+)~', '$1$3', $content);
    }

    public function extractPatchFromSh($content)
    {
        return preg_replace('~(.*__PATCHFILE_FOLLOWS__\s+)~s', '', $content);
    }

    public function convertFromComposerToGitFormat($sourcePath, $destPath = null)
    {
        return $this->convert($sourcePath, '-r', $destPath);
    }

    public function convertFromGitToComposerFormat($sourcePath, $destPath = null)
    {
        return $this->convert($sourcePath, '', $destPath);
    }

    public function preparePatch($path)
    {
        $content = file_get_contents($path);
        if (pathinfo($path, PATHINFO_EXTENSION) == 'sh') {
            $content = $this->extractPatchFromSh($content);
        }
        Filesystem::getInstance()->createTmpFile($path, $content);
    }
}

<?php

require_once 'app/code/File/Filesystem.php';

class Patch_Converter
{
    private $converterToolPath = 'app/bin/patch-converter.php';


    public function convert($sourcePath, $options, $destPath = null)
    {
        $destPath = strlen($destPath)
            ? $destPath
            : $sourcePath;

        exec("php {$this->converterToolPath} {$options} {$sourcePath} > {$destPath}", $output, $status);

        Filesystem::getInstance()->registerTmpFile($destPath);

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
        $content = $this->removePathPrefix($content);
        Filesystem::getInstance()->createTmpFile($path, $content);
    }
}

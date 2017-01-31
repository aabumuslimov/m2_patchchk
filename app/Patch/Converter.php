<?php

class Patch_Converter
{
    const MODULE = 'Module';
    const LIBRARY = 'Library';


    protected $gitPath = [
        self::MODULE    => 'app/code/Magento/',
        self::LIBRARY   => 'lib/internal/Magento/'
    ];

    protected $composerPath = [
        self::MODULE    => 'vendor/magento/module-',
        self::LIBRARY   => 'vendor/magento/'
    ];


    protected function convertDashedStringToCamelCase($string)
    {
        return str_replace('-', '', ucwords($string, '-'));
    }

    protected function camelCaseStringCallbackModule($matches)
    {
        return $this->gitPath[self::MODULE] . $this->convertDashedStringToCamelCase($matches[1]);
    }

    protected function camelCaseStringCallbackLibrary($matches)
    {
        return $this->gitPath[self::LIBRARY] . $this->convertDashedStringToCamelCase($matches[1]);
    }

    public function convertFromComposerToGitFormat($content)
    {
        foreach ($this->composerPath as $type => $path) {
            $content = preg_replace_callback('~' . $path . '([-\w]+)~',
                [$this, 'camelCaseStringCallback' . $type], $content);
        }

        return $content;
    }

    public function removePathPrefix($content)
    {
        return preg_replace('~(\s+)(a/|b/)([^\s]+)~', '$1$3', $content);
    }

    public function extractPatchFromSh($content)
    {
        return preg_replace('~(.*__PATCHFILE_FOLLOWS__\s+)~s', '', $content);
    }

    public function preparePatch($content, $stripSh = false)
    {
        if ($stripSh) {
            $content = $this->extractPatchFromSh($content);
        }
        $content = $this->convertFromComposerToGitFormat($content);

        return $this->removePathPrefix($content);
    }
}

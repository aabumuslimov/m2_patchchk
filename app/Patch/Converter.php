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

    protected function camelCaseStringCallbackModule($value)
    {
        return $this->gitPath[self::MODULE] . $this->convertDashedStringToCamelCase($value[1]);
    }

    protected function camelCaseStringCallbackLibrary($value)
    {
        return $this->gitPath[self::LIBRARY] . $this->convertDashedStringToCamelCase($value[1]);
    }

    public function convertFromComposerToGitFormat($content)
    {
        foreach ($this->composerPath as $type => $path) {
            $content = preg_replace_callback('~(?:a/|b/)' . addcslashes($path, '/') . '([-\w]+)~',
                array($this, 'camelCaseStringCallback' . $type), $content);
        }

        return $content;
    }

    public function extractPatchFromSh($content)
    {
        $pattern = '~(.*__PATCHFILE_FOLLOWS__\s+)~s';
        $content = preg_replace($pattern, '', $content);

        return $content;
    }

    public function preparePatch($content, $stripSh = false)
    {
        if ($stripSh) {
            $content = $this->extractPatchFromSh($content);
        }

        return $this->convertFromComposerToGitFormat($content);
    }
}

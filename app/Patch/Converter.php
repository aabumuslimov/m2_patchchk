<?php

class Patch_Converter
{
    const MODULE            = 'Module';
    const ADMINHTML_DESIGN  = 'AdminhtmlDesign';
    const FRONTEND_DESIGN   = 'FrontendDesign';
    const LIBRARY           = 'Library';


    protected $gitPath = [
        self::MODULE            => 'app/code/Magento/',
        self::ADMINHTML_DESIGN  => 'app/design/adminhtml/Magento/',
        self::FRONTEND_DESIGN   => 'app/design/frontend/Magento/',
        self::LIBRARY           => 'lib/internal/Magento/'
    ];

    protected $composerPath = [
        self::MODULE            => 'vendor/magento/module-',
        self::ADMINHTML_DESIGN  => 'vendor/magento/theme-adminhtml-',
        self::FRONTEND_DESIGN   => 'vendor/magento/theme-frontend-',
        self::LIBRARY           => 'vendor/magento/'
    ];


    protected function convertDashedStringToCamelCase($string)
    {
        return str_replace('-', '', ucwords($string, '-'));
    }

    protected function camelCaseStringCallbackModule($matches)
    {
        return $this->gitPath[self::MODULE] . $this->convertDashedStringToCamelCase($matches[1]);
    }

    protected function camelCaseStringCallbackAdminhtmlDesign($matches)
    {
        return $this->gitPath[self::ADMINHTML_DESIGN] . $matches[1];
    }

    protected function camelCaseStringCallbackFrontendDesign($matches)
    {
        return $this->gitPath[self::FRONTEND_DESIGN] . $matches[1];
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

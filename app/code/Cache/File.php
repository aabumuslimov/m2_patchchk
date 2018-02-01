<?php

class Cache_File
{
    const CACHE_DIR_NAME = 'cache';

    const CACHE_PREFIX = 'patchchk_cache_';


    private $cachePath;


    private function validateWritablePath($path) {
        return is_dir($path) && is_writable($path);
    }

    public function __construct()
    {
        $varPath = BP . DS . VAR_DIR_NAME . DS;
        if ($this->validateWritablePath($varPath)) {
            $cachePath = $varPath . self::CACHE_DIR_NAME . DS;
            if (!file_exists($cachePath)) {
                mkdir($cachePath, 02777);
            } elseif (!is_dir($cachePath) || !is_writable($cachePath)) {
                $cachePath = '/tmp';
                if (!$this->validateWritablePath($cachePath)) {
                    throw new Exception('Cache dir is not writable.');
                }
            }
        }

        $this->cachePath = $cachePath;
    }

    public function loadCache($cacheId)
    {
        $file = $this->cachePath . self::CACHE_PREFIX . $cacheId;
        if (!file_exists($file) || !is_readable($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    public function saveCache($cacheId, $content)
    {
        $file = $this->cachePath . self::CACHE_PREFIX . $cacheId;
        return file_put_contents($file, $content);
    }
}

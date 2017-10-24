<?php

class Filesystem
{
    static private $instance;

    private $tmpFileList;


    static public function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Filesystem();
        }

        return self::$instance;
    }

    private function __clone() {}

    public function __destruct()
    {
        foreach ($this->tmpFileList as $filePath) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    public function registerTmpFile($path)
    {
        if (is_file($path)) {
            $this->tmpFileList[] = $path;
        }

        return $this;
    }

    public function createTmpFile($path, $content)
    {
        file_put_contents($path, $content);
        $this->tmpFileList[] = $path;
    }
}

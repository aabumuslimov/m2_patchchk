<?php

class Patch_Checker
{
    private $instanceList = [];


    public function __construct()
    {
        $instanceListFilePath = BP . 'app/config/instance_list.ini';
        if (!file_exists($instanceListFilePath)) {
            return $this;
        }

        $this->instanceList = parse_ini_file($instanceListFilePath, true, INI_SCANNER_TYPED);
    }

    public function checkPatchForRelease($patchName, $releasePath)
    {
        if ($releasePath == '' || !is_dir($releasePath)) {
            return 'n/a';
        }

        chdir($releasePath);
        exec('patch --dry-run -p0 < ' . BP . UPLOAD_PATH . $patchName, $output, $returnStatus);

        return !$returnStatus;
    }

    public function checkPatchForAllReleases($patchName)
    {
        $result = [];

        foreach ($this->instanceList as $groupName => $groupInstanceList) {
            foreach ($groupInstanceList as $release => $path) {
                if (is_int($path)) {
                    for ($i = 0; $i < $path; $i++) {
                        $result[$groupName][] = ['release_name' => 'n/a', 'check_result' => 'n/a'];
                    }
                    continue;
                }

                $result[$groupName][] = [
                    'release_name' => $release,
                    'check_result' => $this->checkPatchForRelease($patchName, $path)
                ];
            }
        }

        return $result;
    }
}
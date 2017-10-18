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

    public function checkPatchForRelease($command, $releasePath)
    {
        if ($releasePath == '' || !is_dir($releasePath)) {
            return 'n/a';
        }

        chdir($releasePath);
        exec($command, $output, $returnStatus);

        return !$returnStatus;
    }

    public function checkPatchForAllReleases($patchName, $patchNameGit)
    {
        $result = [];

        foreach ($this->instanceList as $groupName => $groupInstanceList) {
            foreach ($groupInstanceList as $release => $path) {
                if (is_int($path)) {
                    for ($i = 0; $i < $path; $i++) {
                        $result[$groupName][] = ['instance_name' => 'n/a', 'check_method' => 'n/a'];
                    }
                    continue;
                }

                $result[$groupName][] = [
                    'instance_name' => $release,
                    'check_method' => [
                        'patch' => $this->checkPatchForRelease('patch --dry-run -p0 < ' . BP . UPLOAD_PATH . $patchName, $path),
                        'git'   => $this->checkPatchForRelease('git apply --check ' . BP . UPLOAD_PATH . $patchNameGit, $path)
                    ]
                ];
            }
        }

        return $result;
    }
}

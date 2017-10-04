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

    public function checkPatchForGitRelease($patchName, $releasePath)
    {
        if ($releasePath == '' || !is_dir($releasePath)) {
            return 'n/a';
        }

        chdir($releasePath);
        exec('git apply --check ' . BP . UPLOAD_PATH . $patchName, $output, $GitStatus);

        $returnGitStatus = ($GitStatus == 0) ? true : false;

        return $returnGitStatus;
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
                        'patch' => $this->checkPatchForRelease($patchName, $path),
                        'git' => $this->checkPatchForGitRelease($patchNameGit, $path)
                    ]
                ];
            }
        }

        return $result;
    }
}

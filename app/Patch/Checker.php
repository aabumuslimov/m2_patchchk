<?php

class Patch_Checker
{
    protected $_eeDeploymentList = array();
    protected $_ceDeploymentList = array();


    public function __construct()
    {
        $instanceListFilePath = BP . 'app/config/instance_list.ini';
        if (!file_exists($instanceListFilePath)) {
            return $this;
        }

        $config = parse_ini_file($instanceListFilePath, true);
        if (isset($config['ee'])) {
            $this->_eeDeploymentList = $config['ee'];
        }
        if (isset($config['ce'])) {
            $this->_eeDeploymentList = $config['ce'];
        }
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

//    public function checkMergedPathForRelease($patchName, $release)
//    {
//        if (!count($patchName['files'])) {
//            return 'n/a';
//        }
//
//        $sourceFolder = DEPLOYMENTS_BASE_PATH . $release[1] . DIRECTORY_SEPARATOR;
//        if (!is_dir($sourceFolder)) {
//            return 'n/a';
//        }
//
//        foreach ($patchName['files'] as $file) {
//            $dirname = dirname($file);
//            if (!empty($dirname) && $dirname != '.' && !is_dir($dirname)) {
//                mkdir($dirname, 0777, true);
//            }
//            $sourcePath = $sourceFolder . $file;
//            if (!file_exists($sourcePath) || file_exists($file)) {
//                continue;
//            }
//            copy($sourcePath, $file);
//        }
//
//        $returnStatus = 0;
//        $output = array();
//
//        exec('patch -p0 < ' . UPLOAD_PATH . $patchName['name'], $output, $returnStatus);
//        exec('rm -Rf *');
//
//        return !$returnStatus;
//    }

    public function checkPatchForAllReleases($patchName, $withoutDryRun = false)
    {
        $result = array();

//        if (!$withoutDryRun) {
            foreach ($this->_eeDeploymentList as $release => $path) {
                $result['ee'][] = array(
                    'release_name' => $release,
                    'check_result' => $this->checkPatchForRelease($patchName, $path)
                );
            }
            foreach ($this->_ceDeploymentList as $release => $path) {
                $result['ce'][] = array(
                    'release_name' => $release,
                    'check_result' => $this->checkPatchForRelease($patchName, $path)
                );
            }
//        } else {
//            $workDirectory = $this->_initWorkDirectory();
//            chdir($workDirectory);
//            foreach ($this->_eeDeploymentList as $release => $path) {
//                $result['ee'][] = array(
//                    'release_name' => $release,
//                    'check_result' => $this->checkMergedPathForRelease($patchName, array($release => $path))
//                );
//            }
//            foreach ($this->_ceDeploymentList as $release => $path) {
//                $result['ce'][] = array(
//                    'release_name' => $release,
//                    'check_result' => $this->checkMergedPathForRelease($patchName, array($release => $path)),
//                );
//            }
//
//            exec('rm -Rf ' . $workDirectory);
//        }

        return $result;
    }

//    public function mergePatchesToOne($patches, $names)
//    {
//        $temporaryPatchName = md5(uniqid(time())) . '.merged.patch';
//        exec('touch ' . UPLOAD_PATH . $temporaryPatchName);
//
//        foreach ($patches as $fileId => $patch) {
//            exec(sprintf(
//                'cat %s >> %s',
//                UPLOAD_PATH . $patch,
//                UPLOAD_PATH . $temporaryPatchName
//            ));
//            exec('rm -Rf ' . UPLOAD_PATH . $patch);
//        }
//
//        exec('grep "diff --git" ' . UPLOAD_PATH . $temporaryPatchName . ' | awk \'{print $3}\'', $files);
//
//        return array(
//            'name' => $temporaryPatchName,
//            'files' => $files
//        );
//    }

//    protected function _initWorkDirectory()
//    {
//        $workDirectory = UPLOAD_PATH . md5(uniqid(time()));
//        mkdir($workDirectory, 0777);
//        return $workDirectory;
//    }

    public function drawResults($results) {
        $output = '';
        foreach ($results as $release) {
            $output .= '<tr><td';
            if ($release['check_result'] === 'n/a') {
                if ($release['release_name'] === 'n/a') {
                    $column_content = '&nbsp;';
                } else {
                    $column_content = $release['release_name'];
                }
                $output .= ' colspan="2">' . $column_content;
            } else {
                $output .= '>' . $release['release_name'] . '</td>';
                if ($release['check_result'] == true) {
                    $output .= '<td class="td_ok">Ok';
                } else {
                    $output .= '<td class="td_fail">No';
                }
            }
            $output .= '</td></tr>';
        }
        return $output;
    }
}

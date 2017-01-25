<?php

/**
 * Support M2 patch checker tool v.0.4.0
 */

require_once('app/bootstrap.php');

require_once('app/File/Uploader.php');
require_once('app/Patch/Checker.php');
require_once('app/Patch/Converter.php');

$action = (isset($_GET['action'])) ? $_GET['action'] : false;
$messageList = array();

try {
    if ($action == 'upload' && !empty($_POST)) {
        $fileUploader = new File_Uploader(array('upload_path' => BP . UPLOAD_PATH));
        $result = $fileUploader->upload();

        try {
            $patchChecker = new Patch_Checker();

            //        if (count($result['new_file_name']) == 1) {
            $checkResults = $patchChecker->checkPatchForAllReleases($result['new_file_name'][0]);
            //        } else {
            //            $mergedPatch = $patchChecker->mergePatchesToOne($result['new_file_name'], $result['result']['filename']);
            //            $checkResults = $patchChecker->checkPatchForAllReleases($mergedPatch, true);
            //            @unlink(UPLOAD_PATH . $mergedPatch['name']);
            //        }

            if (isset($result['result'])) {
                $collectStats = true;
                $statsPath = BP . STATS_PATH;
                if (!file_exists($statsPath)) {
                    $collectStats = mkdir($statsPath, 0777, true);
                }

                foreach ($result['result']['filename'] as $fileId => $filename) {
                    $uploadedFilePath = BP . UPLOAD_PATH . $result['new_file_name'][$fileId];
                    if (file_exists($uploadedFilePath)) {
                        @unlink($uploadedFilePath);
                    }

                    if ($collectStats) {
                        // checked patches statistic collection
                        $data = date('Y-m-d H:i:s') . ': ' . $filename . "\n";
                        file_put_contents($statsPath . 'stats.log', $data, FILE_APPEND);
                    }
                }
            }

            $result = $result['result'];
            $result['checkResults'] = $checkResults;

            if (!NO_AJAX) {
                echo json_encode($result);
                die;
            }
        } catch (Exception $e) {
            if (isset($result['result'])) {
                foreach ($result['result']['filename'] as $fileId => $filename) {
                    $uploadedFilePath = BP . UPLOAD_PATH . $result['new_file_name'][$fileId];
                    if (file_exists($uploadedFilePath)) {
                        @unlink($uploadedFilePath);
                    }
                }
            }

            throw $e;
        }
    }
} catch (Exception $e) {
    $messageList[] .= '<span class="error_span">' . $e->getMessage() . '</span>';
}

$messageList = implode('<br/>', $messageList);

require_once 'design/templates/index.phtml';

<?php

/**
 * Support M2 patch checker tool v.0.4.0
 */

require_once('app/bootstrap.php');

require_once('app/File/Uploader.php');
require_once('app/Patch/Checker.php');
require_once('app/Patch/Converter.php');

$action = (isset($_GET['action'])) ? $_GET['action'] : false;
$messageList = [];

try {
    if ($action == 'upload' && !empty($_POST)) {
        $fileUploader = new File_Uploader(['upload_path' => BP . UPLOAD_PATH]);
        $result = $fileUploader->upload();

        $patchChecker = new Patch_Checker();
        //        if (count($result['new_file_name']) == 1) {
        $checkResults = $patchChecker->checkPatchForAllReleases($result['new_file_name'][0]);
        //        } else {
        //            $mergedPatch = $patchChecker->mergePatchesToOne($result['new_file_name'], $result['result']['filename']);
        //            $checkResults = $patchChecker->checkPatchForAllReleases($mergedPatch, true);
        //            @unlink(UPLOAD_PATH . $mergedPatch['name']);
        //        }
        $result = $result['result'];
        $result['checkResults'] = $checkResults;

        // checked patches statistic collection
        if (isset($result['filename'])) {
            $statsPath = BP . STATS_PATH;
            if (file_exists($statsPath) || mkdir($statsPath, 0777, true)) {
                foreach ($result['filename'] as $fileId => $filename) {
                    $data = date('Y-m-d H:i:s') . ': ' . $filename . "\n";
                    file_put_contents($statsPath . 'stats.log', $data, FILE_APPEND);
                }
            }
        }

        if (!NO_AJAX) {
            echo json_encode($result);
            die;
        }
    }
} catch (Exception $e) {
    $messageList[] .= '<span class="error_span">' . $e->getMessage() . '</span>';
}

$messageList = implode('<br/>', $messageList);

require_once 'design/templates/index.phtml';

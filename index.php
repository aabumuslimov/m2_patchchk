<?php

require_once 'app/bootstrap.php';

$action = (isset($_GET['action'])) ? $_GET['action'] : false;

try {
    if ($action == 'upload' && !empty($_POST)) {
        require_once 'app/code/File/Uploader.php';
        $fileUploader = new File_Uploader(['upload_path' => BP . UPLOAD_PATH]);
        $result = $fileUploader->upload();

        require_once 'app/code/Patch/Checker.php';
        $patchChecker = new Patch_Checker(BP . UPLOAD_PATH . $result['new_file_name'][0]);
        $checkResults = $patchChecker->checkPatchForAllReleases();
        $result = $result['result'];
        $result['check_results'] = $checkResults;

        // checked patches statistic collection
        if (isset($result['filename'])) {
            $statsPath = BP . STATS_PATH;
            if (file_exists($statsPath) || mkdir($statsPath, 02777, true)) {
                foreach ($result['filename'] as $fileId => $filename) {
                    $data = date('Y-m-d H:i:s') . ': ' . $filename . "\n";
                    file_put_contents($statsPath . 'stats.log', $data, FILE_APPEND);
                }
            }
        }

        echo json_encode($result);
        die;
    }
} catch (Exception $e) {
    // @TODO Implement logging
}

require_once 'app/code/Design.php';
$design = new Design();

require_once 'design/templates/index.phtml';

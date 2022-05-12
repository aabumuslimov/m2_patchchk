<?php

require_once 'app/bootstrap.php';

$action = (isset($_GET['action'])) ? $_GET['action'] : false;

try {
    if ($action == 'upload' && !empty($_POST)) {
        $fileUploader = new \Magento\PatchChecker\File\Uploader(['upload_path' => BP . UPLOAD_PATH]);
        $result = $fileUploader->upload();

        $patchChecker = new \Magento\PatchChecker\Patch\Checker(
            new \Magento\PatchChecker\Deploy\InstanceManager(),
            new \Magento\PatchChecker\Patch\Check\StrategyManager(),
            new \Magento\PatchChecker\Patch\InstancePatchConverter(new \Magento\PatchChecker\Patch\Converter())
        );
        $checkResults = $patchChecker->check(BP . UPLOAD_PATH . $result['new_file_name'][0]);
        $result = $result['result'];
        $result['check_results'] = $checkResults;
        $result['check_method'] = 'file';
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
    } elseif (!empty($_POST['patch_id'])) {
        $result = [
            'check_results' => [],
            'check_method' => 'mqp',
            'error' => '',
        ];
        try {
            $patchChecker = new \Magento\PatchChecker\Patch\MQP\Checker(
                new \Magento\PatchChecker\Deploy\InstanceManager(),
                new \Magento\PatchChecker\Patch\MQP\PatchRepository(new \Magento\QualityPatches\Info()),
                new \Magento\PatchChecker\Patch\MQP\VersionsManager
            );
            $result['check_results'] = $patchChecker->check($_POST['patch_id']);
            $result['check_method'] = 'mqp';
        } catch (\Exception $exception) {
            $mqpVersion = new \Magento\PatchChecker\Patch\MQP\Version();
            $result['error'] = "Patch ID '{$_POST['patch_id']}' is not found in MQP $mqpVersion";
        }

        echo json_encode($result);
        die;
    } elseif (!empty($argv[1])) {
        $result = [
            'check_results' => [],
            'error' => '',
            'new_file_name'=> $argv[1]
        ];
        $patchChecker = new \Magento\PatchChecker\Patch\Checker(
            new \Magento\PatchChecker\Deploy\InstanceManager(),
            new \Magento\PatchChecker\Patch\Check\StrategyManager(),
            new \Magento\PatchChecker\Patch\InstancePatchConverter(new \Magento\PatchChecker\Patch\Converter())
        );
        $checkResults = $patchChecker->check($result['new_file_name']);
        $result['check_results'] = $checkResults;

        echo json_encode($result);
        die;
    }
} catch (Exception $e) {
    // @TODO Implement logging
}

$design = new \Magento\PatchChecker\Design();
$mqpVersion = new \Magento\PatchChecker\Patch\MQP\Version();

require_once 'design/templates/index.phtml';

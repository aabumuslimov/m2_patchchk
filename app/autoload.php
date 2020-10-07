<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

$vendorAutoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($vendorAutoloadPath)) {
    throw new RuntimeException('Required file \'autoload.php\' was not found.');
}
require $vendorAutoloadPath;

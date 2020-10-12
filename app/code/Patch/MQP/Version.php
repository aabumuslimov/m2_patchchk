<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\MQP;

use Magento\PatchChecker\Util;

/**
 * MQP version
 */
class Version
{
    const PACKAGE_NAME = 'magento/quality-patches';
    const UNKNOWN = 'unknown';

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        $packages = [];
        if (file_exists(Util::getAbsolutePath('composer.lock'))) {
            $lock = Util::getJsonFile('composer.lock');
            $packages = array_column($lock['packages'], 'version', 'name');
        }
        return $packages[self::PACKAGE_NAME] ?? self::UNKNOWN;
    }
}
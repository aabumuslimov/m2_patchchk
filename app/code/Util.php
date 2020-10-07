<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker;

/**
 * Util class
 */
class Util
{
    /**
     * @param string $path
     * @return array
     * @throws \Exception
     */
    public static function getJsonFile(string $path): array
    {
        $content = file_get_contents($path);
        $result = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(
                "Unable to unserialize json file '{$path}'. Error: " . json_last_error_msg()
            );
        }
        return $result;
    }
}

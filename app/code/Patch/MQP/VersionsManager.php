<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\MQP;

use Magento\PatchChecker\Util;

/**
 * Versions manager class
 */
class VersionsManager
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @param string $coreVersion
     * @param string $packageName
     * @return string|null
     */
    public function getPackageVersion(string $coreVersion, string $packageName)
    {
        return $this->getConfiguration()[$coreVersion][$packageName] ?? null;
    }

    /**
     * Get versions configuration
     *
     * @return array
     */
    private function getConfiguration(): array
    {
        if ($this->configuration === null) {
            $this->configuration = [];
            $configPath = $this->getConfigurationPath();
            if (file_exists($configPath)) {
                $this->configuration = Util::getJsonFile($configPath);
            }
        }

        return $this->configuration;
    }

    /**
     * Get versions configuration path
     *
     * @return string
     */
    private function getConfigurationPath(): string
    {
        return Util::getAbsolutePath('vendor/magento/quality-patches/magento_releases.json');
    }
}

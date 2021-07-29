<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\MQP;

use Magento\PatchChecker\Util;
use Magento\PatchChecker\Patch\MQP\Data\AggregatedPatch;
use Magento\QualityPatches\Info;

/**
 * MQP patches repository
 */
class PatchRepository
{
    /**
     * @var Info
     */
    private $info;

    /**
     * @param Info $info
     */
    public function __construct(Info $info)
    {
        $this->info = $info;
    }


    /**
     * Find patch by ID
     *
     * @param string $id
     * @return AggregatedPatch
     * @throws \Exception
     */
    public function findOne(string $id): AggregatedPatch
    {
        $config = $this->getConfiguration();
        if (isset($config[$id])) {
            return new AggregatedPatch($config[$id]);
        }
        throw new \Exception("Patch '$id' cannot be found.");
    }

    /**
     * Get patches configuration
     *
     * @return array
     * @throws \Exception
     */
    private function getConfiguration(): array
    {
        $result = [];
        $configPath = $this->info->getSupportPatchesConfig();
        if (file_exists($configPath)) {
            $result = Util::getJsonFile($configPath);
        }

        return $result;
    }
}

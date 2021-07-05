<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\MQP\Data;

/**
 * Aggregated patch data class.
 */
class AggregatedPatch
{
    /**
     * @var array
     */
    private $config;
    /**
     * @var array
     */
    private $patches;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get patch items
     *
     * @return Patch[]
     */
    public function getPatches(): array
    {
        if ($this->patches === null) {
            $this->patches = [];
            foreach ($this->config['packages'] as $packageName => $packageConfiguration) {
                foreach ($packageConfiguration as $packageConstraint => $patchData) {
                    $this->patches[] = new Patch($packageName, $packageConstraint, $patchData['file']);
                }
            }
        }

        return $this->patches;
    }
}

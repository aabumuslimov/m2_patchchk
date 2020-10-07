<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\MQP\Data;

/**
 * Patch data
 */
class Patch
{
    /**
     * @var string
     */
    private $packageName;
    /**
     * @var string
     */
    private $packageConstraint;
    /**
     * @var string
     */
    private $filename;

    /**
     * @param string $packageName
     * @param string $packageConstraint
     * @param string $filename
     */
    public function __construct(
        string $packageName,
        string $packageConstraint,
        string $filename
    ) {
        $this->packageName = $packageName;
        $this->packageConstraint = $packageConstraint;
        $this->filename = $filename;
    }

    /**
     * Get package name
     *
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * Get package constraints
     *
     * @return string
     */
    public function getPackageConstraint(): string
    {
        return $this->packageConstraint;
    }

    /**
     * Get patch filename
     *
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }
}

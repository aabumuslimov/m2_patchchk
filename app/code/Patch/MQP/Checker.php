<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\PatchChecker\Patch\MQP;

use Composer\Semver\Semver;
use Magento\PatchChecker\Deploy\Instance;
use Magento\PatchChecker\Deploy\InstanceManager;
use Magento\PatchChecker\Patch\AbstractChecker;

/**
 * MQP patch checker
 */
class Checker extends AbstractChecker
{
    /**
     * @var VersionsManager
     */
    private $versionsManager;
    /**
     * @var PatchRepository
     */
    private $patchRepository;

    /**
     * @param InstanceManager $instanceManager
     * @param PatchRepository $patchRepository
     * @param VersionsManager $versionsManager
     */
    public function __construct(
        InstanceManager $instanceManager,
        PatchRepository $patchRepository,
        VersionsManager $versionsManager
    ) {
        parent::__construct($instanceManager);
        $this->versionsManager = $versionsManager;
        $this->patchRepository = $patchRepository;
    }

    /**
     * @inheritDoc
     */
    public function getResult(Instance $instance, string $patch): int
    {
        $aggregatedPatch = $this->patchRepository->findOne($patch);
        $status = self::PATCH_APPLY_RESULT_FAILED;
        foreach ($aggregatedPatch->getPatches() as $patch) {
            $packageVersion = $this->versionsManager->getPackageVersion(
                $instance->getInstanceName(),
                $patch->getPackageName()
            );
            if ($packageVersion && Semver::satisfies($packageVersion, $patch->getPackageConstraint())) {
                $status = self::PATCH_APPLY_RESULT_SUCCESSFUL;
                break;
            }
        }
        return $status;
    }
}

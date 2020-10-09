<?php

namespace SM\Installation\Api;

interface InstallationRepositoryInterface
{
    /**
     * @param int $cartId
     * @param int $itemId
     * @param string $action
     * @param int $useInstallation
     * @param string $installationNote
     * @return bool
     */
    public function save($cartId, $itemId, $action, $useInstallation, $installationNote);
}

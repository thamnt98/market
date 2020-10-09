<?php

declare(strict_types=1);

namespace SM\Email\Api\Repository;

/**
 * Interface SenderRepositoryInterface
 * @package SM\Email\Api
 */
interface SendEmailRepositoryInterface
{
    /**
     * @param string|int $templateId
     * @param string $sender
     * @param string $email
     * @param string|null $name
     * @param array $templateParams
     * @param int|null $storeId
     * @param string $area
     */
    public function send(
        $templateId,
        $sender,
        $email,
        $name,
        $templateParams,
        $storeId,
        $area
    );
}

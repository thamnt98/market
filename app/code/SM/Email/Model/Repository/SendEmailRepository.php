<?php

declare(strict_types=1);

namespace SM\Email\Model\Repository;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use SM\Email\Api\Repository\SendEmailRepositoryInterface;

/**
 * Class SendEmailRepository
 * @package SM\Email\Model
 */
class SendEmailRepository implements SendEmailRepositoryInterface
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Sender constructor.
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        TransportBuilder $transportBuilder
    ) {
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param string|int $templateId
     * @param string $sender
     * @param string $email
     * @param string|null $name
     * @param array $templateParams
     * @param int|null $storeId
     * @param string $area
     * @throws LocalizedException
     * @throws MailException
     */
    public function send(
        $templateId,
        $sender,
        $email,
        $name,
        $templateParams,
        $storeId,
        $area
    ) {
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setFromByScope($sender, $storeId)
            ->addTo($email, $name)
            ->setTemplateVars($templateParams)
            ->setTemplateOptions(['area' => $area, 'store' => $storeId])
            ->getTransport();

        $transport->sendMessage();
    }
}

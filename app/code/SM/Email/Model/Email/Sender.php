<?php

declare(strict_types=1);

namespace SM\Email\Model\Email;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\Store;

class Sender
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
        string $sender,
        string $email,
        string $name = null,
        array $templateParams = [],
        int $storeId = Store::DEFAULT_STORE_ID,
        string $area = Area::AREA_FRONTEND
    ): void {
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setFromByScope($sender, $storeId)
            ->addTo($email, $name)
            ->setTemplateVars($templateParams)
            ->setTemplateOptions(['area' => $area, 'store' => $storeId])
            ->getTransport();

        $transport->sendMessage();
    }

    /**
     * @param string|int $templateId
     * @param string $sender
     * @param array|string $email
     * @param string|null $name
     * @param array $templateParams
     * @param int|null $storeId
     * @param string $area
     * @throws LocalizedException
     * @throws MailException
     */
    public function sends(
        $templateId,
        string $sender,
        $email,
        string $name = null,
        array $templateParams = [],
        int $storeId = Store::DEFAULT_STORE_ID,
        string $area = Area::AREA_FRONTEND
    ): void {
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

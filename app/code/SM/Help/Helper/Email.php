<?php

/**
 * @category SM
 * @package SM_TodayDeal
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Help\Helper;

use Magento\Customer\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use SM\Email\Model\Email\Sender as EmailSender;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Email
{
    const XML_PATH_HELP_SEND_EMAIL_TEMPLATE = 'sm_help/email/template';
    const XML_PATH_HELP_SEND_EMAIL_SENDER = 'sm_help/email/sender';

    /**
     * @var EmailSender
     */
    protected $sendEmailRepository;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Constructor
     *
     * @param Session $customerSession
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param EmailSender $emailRepository
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session $customerSession,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        EmailSender $emailRepository,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->sendEmailRepository = $emailRepository;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * Send email to customer
     * @param int $storeId
     * @param string $email
     */
    public function sendEmail($storeId = 0, $email = '')
    {
        try {
            if ($email) {
                $this->inlineTranslation->suspend();
                $sender = $this->getHelpEmailSender($storeId);
                $templateId = $this->getHelpEmailTemplate($storeId);
                $this->sendEmailRepository->send(
                    $templateId,
                    $sender,
                    $email,
                    $name = null,
                    $templateParams = [],
                    $storeId,
                    $area = \Magento\Framework\App\Area::AREA_FRONTEND
                );
                $this->inlineTranslation->resume();
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getHelpEmailTemplate(int $storeId): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_HELP_SEND_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $storeId
     * @return string
     */
    public function getHelpEmailSender($storeId): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_HELP_SEND_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}

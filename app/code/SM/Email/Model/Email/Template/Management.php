<?php

declare(strict_types=1);

namespace SM\Email\Model\Email\Template;

use Magento\Email\Model\Template;
use Magento\Email\Model\TemplateFactory as EmailTemplateFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use SM\Email\Api\Repository\EmailTemplateRepositoryInterface;

/**
 * Class Management
 * @package Rina\CustomerEmailNotification\Model
 */
class Management
{
    /**
     * @var EmailTemplateRepositoryInterface
     */
    protected $emailTemplateRepository;

    /**
     * @var EmailTemplateFactory
     */
    protected $emailTemplateFactory;

    /**
     * Management constructor.
     * @param EmailTemplateRepositoryInterface $emailTemplateRepository
     * @param EmailTemplateFactory $emailTemplateFactory
     */
    public function __construct(
        EmailTemplateRepositoryInterface $emailTemplateRepository,
        EmailTemplateFactory $emailTemplateFactory
    ) {
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->emailTemplateFactory = $emailTemplateFactory;
    }

    /**
     * @param string $code
     * @param int $type
     * @param string $subject
     * @param string $content
     * @return Template
     * @throws CouldNotSaveException
     */
    public function create(string $code, int $type, string $subject, string $content): Template
    {
        /** @var Template $template */
        $template = $this->emailTemplateFactory->create();
        $template->setTemplateCode($code);
        $template->setTemplateType($type);
        $template->setTemplateSubject($subject);
        $template->setTemplateText($content);

        return $this->emailTemplateRepository->save($template);
    }
}

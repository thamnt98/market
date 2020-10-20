<?php

namespace SM\Review\Plugin;

use Exception;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Review\Model\Review;
use Magento\Store\Model\Store;
use SM\Email\Model\Repository\SendEmailRepository;

/**
 * Class SaveReview
 * @package SM\Review\Plugin
 */
class ReviewModel
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;
    /**
     * @var StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;
    /**
     * @var SendEmailRepository
     */
    protected $sendEmailRepository;

    /**
     * SaveReview constructor.
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerRepository $customerRepository
     * @param DataObjectFactory $dataObjectFactory
     * @param SendEmailRepository $sendEmailRepository
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        CustomerRepository $customerRepository,
        DataObjectFactory $dataObjectFactory,
        SendEmailRepository $sendEmailRepository
    ) {
        $this->sendEmailRepository = $sendEmailRepository;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param Review $subject
     * @param Review $result
     * @return Review
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws Exception
     */
    public function afterSave(Review $subject, Review $result)
    {
        if ($result->getStatusId() == 1 && $result->getData("is_email_sent") == 0) {
            $customer = $this->customerRepository->getById($result->getCustomerId());
            if ($this->sendEmail($customer->getEmail(), $result, $customer)) {
                $result->setData("is_email_sent", 1)->save();
            }
        }
        return $result;
    }

    /**
     * @param string $receiver
     * @param Review $review
     * @return bool
     */
    public function sendEmail($receiver, $review, $customer)
    {
        try {
            $requestData = [];
            $requestData["title"] = $review->getTitle();
            $requestData["detail"] = $review->getDetail();

            $templateVars = [
                'name' => $customer->getFirstname(),
                'email' => $customer->getEmail()
            ];
            /** @var DataObject $postObject */
            $postObject = $this->dataObjectFactory->create();
            $postObject->setData($requestData);

            $this->sendEmailRepository->send(
                $this->scopeConfig->getValue(
                    'sm_review/email_post/email_template',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                $this->scopeConfig->getValue(
                    'sm_review/email_post/email_identity',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                $receiver,
                "",
                $templateVars,
                Store::DEFAULT_STORE_ID,
                Area::AREA_FRONTEND
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

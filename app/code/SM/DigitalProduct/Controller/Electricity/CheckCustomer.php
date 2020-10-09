<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Controller\Index
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Controller\Electricity;

use Magento\Catalog\Helper\Image;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use SM\DigitalProduct\Api\Data\Inquire\ElectricityPostPaidBillDataInterface;
use SM\DigitalProduct\Api\Data\Inquire\ResponseDataInterface;
use SM\DigitalProduct\Api\ReorderRepositoryInterface;
use SM\DigitalProduct\Block\Electricity\Bill\CustomerInformation as BillInformation;
use SM\DigitalProduct\Block\Electricity\Token\CustomerInformation as TokenInformation;
use SM\DigitalProduct\Model\InquireRepository;
use SM\DigitalProduct\Model\OperatorIconRepository;

/**
 * Class CheckCustomer
 * @package SM\DigitalProduct\Controller\Index
 */
class CheckCustomer extends Action
{
    const CUSTOMER_INFO_TOKEN_TEMPLATE = "SM_DigitalProduct::electricity/token/customer-info.phtml";
    const CUSTOMER_INFO_BILL_TEMPLATE = "SM_DigitalProduct::electricity/bill/customer-info.phtml";
    const REGEX_DIGITS_PATTERN = "/^[0-9]*$/";
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var PageFactory
     */
    protected $pageFactory;
    /**
     * @var InquireRepository
     */
    protected $inquireRepository;

    /**
     * @var OperatorIconRepository
     */
    protected $operatorIconRepository;

    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * CheckCustomer constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CustomerRepository $customerRepository
     * @param PageFactory $pageFactory
     * @param InquireRepository $inquireRepository
     * @param OperatorIconRepository $operatorIconRepository
     * @param CurrentCustomer $currentCustomer
     * @param Image $imageHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CustomerRepository $customerRepository,
        PageFactory $pageFactory,
        InquireRepository $inquireRepository,
        OperatorIconRepository $operatorIconRepository,
        CurrentCustomer $currentCustomer,
        Image $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
        $this->currentCustomer = $currentCustomer;
        $this->operatorIconRepository = $operatorIconRepository;
        $this->inquireRepository = $inquireRepository;
        $this->pageFactory = $pageFactory;
        $this->customerRepository = $customerRepository;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var Json $jsonData */
        $jsonData = $this->jsonFactory->create();

        if (!isset($data["customer_id"])) {
            $jsonData->setData([
                "status" => 0,
                "message" => __("Please enter your meter number/customer ID")
            ]);
        } else {
            $numberLength = strlen($data["customer_id"]);
            if ($numberLength == 0) {
                $jsonData->setData([
                    "status" => 0,
                    "message" => __("Please enter your meter number/customer ID")
                ]);
            } elseif (!preg_match(self::REGEX_DIGITS_PATTERN, $data["customer_id"]) || $numberLength > 16) {
                $jsonData->setData([
                    "status" => 0,
                    "message" => __("Make sure you enter the correct number")
                ]);
            } elseif (!isset($data["type"]) || !isset($data["product_id_vendor"])) {
                $jsonData->setData([
                    "status" => 0,
                    "message" => __("Something went wrong. Please refresh page and try again.")
                ]);
            } else {
                $customerNumber = $data["customer_id"];
                $customerId = $this->currentCustomer->getCustomerId();
                /** @var Page $resultPage */
                $resultPage = $this->pageFactory->create();

                if ($data["type"] == \SM\DigitalProduct\Helper\Category\Data::ELECTRICITY_BILL_VALUE) {
                    $result = $this->getPostPaidInfoBlock(
                        $resultPage,
                        $customerNumber,
                        $customerId,
                        $data["product_id_vendor"]
                    );
                } else {
                    $result = $this->getPrePaidInfoBlock(
                        $resultPage,
                        $customerNumber,
                        $customerId,
                        $data["product_id_vendor"]
                    );
                }

                if ($result["status"] == 0) {
                    $jsonData->setData($result);
                } else {
                    $jsonData->setData([
                        "status" => $result["status"],
                        "result" => [
                            "operator_icon" => $this->getPlaceHolderImage(),
                            "info_block" => $result["info_block"]
                        ]
                    ]);
                }
            }
        }
        return $jsonData;
    }

    /**
     * @param Page $resultPage
     * @param string $customerNumber
     * @param int $customerId
     * @param int $productId
     * @return array
     */
    private function getPrePaidInfoBlock($resultPage, $customerNumber, $customerId, $productId)
    {
        /** @var TokenInformation $infoBlock */
        $infoBlock = $resultPage->getLayout()
            ->createBlock(TokenInformation::class)
            ->setTemplate(self::CUSTOMER_INFO_TOKEN_TEMPLATE);

        $information = $this->inquireRepository->inquireElectricityPrePaid(
            $customerNumber,
            $customerId,
            $productId
        );

        return $this->resultProcess($information, $infoBlock);
    }

    /**
     * @param Page $resultPage
     * @param string $customerNumber
     * @param int $customerId
     * @param int $productId
     * @return array
     */
    private function getPostPaidInfoBlock($resultPage, $customerNumber, $customerId, $productId)
    {
        /** @var BillInformation $infoBlock */
        $infoBlock = $resultPage->getLayout()
            ->createBlock(BillInformation::class)
            ->setTemplate(self::CUSTOMER_INFO_BILL_TEMPLATE);

        $information = $this->inquireRepository->inquireElectricityPostPaid(
            $customerNumber,
            $customerId,
            $productId
        );

        return $this->resultProcess($information, $infoBlock,
            ElectricityPostPaidBillDataInterface::TOTAL_ELECTRICITY_BILL);
    }

    /**
     * @return string
     */
    private function getPlaceHolderImage()
    {
        return $this->imageHelper->getDefaultPlaceholderUrl('image');
    }

    /**
     * @param ResponseDataInterface $information
     * @param $infoBlock
     * @param null $type
     * @return array
     */
    private function resultProcess($information, $infoBlock, $type = null)
    {
        if (in_array($information->getResponseCode(), [
            ReorderRepositoryInterface::SUCCESS,
            ReorderRepositoryInterface::TIMEOUT_RESPONSE_CODE,
            ReorderRepositoryInterface::PROVIDER_CUT_OFF,
            ReorderRepositoryInterface::ALREADY_PAID
        ])) {
            $result = [
                "info_block" => $infoBlock->setInformation($information)->toHtml()
            ];
            if ($information->getResponseCode() == ReorderRepositoryInterface::SUCCESS) {
                $result["status"] = 1;
            } elseif ($type == ElectricityPostPaidBillDataInterface::TOTAL_ELECTRICITY_BILL) {
                $result = $this->handleElectricityBillMessage($information->getResponseCode());
            } else {
                $result = [
                    "status" => 0,
                    "message" => __("Make sure you enter the correct number")
                ];
            }
        } else {
            $result = [
                "status" => 0,
                "message" => __("Make sure you enter the correct number")
            ];
        }

        return $result;
    }

    /**
     * @param string $responseCode
     * @return array
     */
    private function handleElectricityBillMessage(string $responseCode)
    {
        $result['status'] = 0;

        switch ($responseCode) {
            case ReorderRepositoryInterface::ALREADY_PAID:
                $result['message'] = __("Your bill already paid or not available");
                break;
            case ReorderRepositoryInterface::PROVIDER_CUT_OFF:
                $result['message'] = __("This service is not available during cut off/maintenance time");
                break;
            case ReorderRepositoryInterface::TIMEOUT_RESPONSE_CODE:
                $result['message'] =  __("Please check the internet connection and try again");
                break;
            default:
                $result['message'] = __("Make sure you enter the correct number");
        }

        return $result;
    }
}

<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Controller\MobileTopup
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use SM\DigitalProduct\Api\Data\C1CategoryDataInterface;
use SM\DigitalProduct\Api\Data\OperatorDataInterface;
use SM\DigitalProduct\Block\Index\ProductList;
use SM\DigitalProduct\Helper\Category\Data;
use SM\DigitalProduct\Model\CategoryRepository;
use SM\DigitalProduct\Model\DigitalProductRepository;
use SM\DigitalProduct\Model\OperatorIconRepository;
use Trans\DigitalProduct\Model\DigitalProductOperatorListRepository;

/**
 * Class CheckPrefix
 * @package SM\DigitalProduct\Controller\MobileTopup
 */
class CheckPrefix extends Action
{
    const PRODUCT_LIST_TEMPLATE = [
        CategoryRepository::TOPUP => "SM_DigitalProduct::index/products.phtml",
        CategoryRepository::MOBILE_PACKAGE => "SM_DigitalProduct::mobilepackage/products.phtml"
    ];
    const REGEX_DIGITS_PATTERN = "/^[0-9]*$/";

    /**
     * @var DigitalProductRepository
     */
    protected $digitalProductRepository;
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var DigitalProductOperatorListRepository
     */
    protected $operatorListRepository;
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var OperatorIconRepository
     */
    protected $operatorIconRepository;

    /**
     * @var CategoryRepository
     */
    protected $digitalCategoryRepository;

    /**
     * CheckPrefix constructor.
     * @param Context $context
     * @param DigitalProductRepository $digitalProductRepository
     * @param JsonFactory $jsonFactory
     * @param DigitalProductOperatorListRepository $operatorListRepository
     * @param PageFactory $pageFactory
     * @param OperatorIconRepository $operatorIconRepository
     * @param CategoryRepository $digitalCategoryRepository
     */
    public function __construct(
        Context $context,
        DigitalProductRepository $digitalProductRepository,
        JsonFactory $jsonFactory,
        DigitalProductOperatorListRepository $operatorListRepository,
        PageFactory $pageFactory,
        OperatorIconRepository $operatorIconRepository,
        CategoryRepository $digitalCategoryRepository
    ) {
        $this->digitalCategoryRepository = $digitalCategoryRepository;
        $this->operatorIconRepository = $operatorIconRepository;
        $this->pageFactory = $pageFactory;
        $this->jsonFactory = $jsonFactory;
        $this->operatorListRepository = $operatorListRepository;
        $this->digitalProductRepository = $digitalProductRepository;
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

        if (!isset($data["number"])) {
            $jsonData->setData([
                "status" => 0,
                "message" => __("Please enter your mobile number")
            ]);
        } else {
            $numberLength = strlen($data["number"]);
            if ($numberLength == 0) {
                $jsonData->setData([
                    "status" => 0,
                    "message" => __("Please enter your mobile number")
                ]);
            } elseif (!preg_match(self::REGEX_DIGITS_PATTERN, $data["number"])) {
                $jsonData->setData([
                    "status" => 0,
                    "message" => __("Please enter a valid mobile number")
                ]);
            } elseif ($numberLength < 8 || $numberLength > 15) {
                $jsonData->setData([
                    "status" => 0,
                    "message" => __("We cannot find this number. Make sure you enter the correct mobile number")

                ]);
            } elseif (!isset($data["category_id"]) || !isset($data["type"])) {
                $jsonData->setData([
                    "status" => 0,
                    "message" => __("Something went wrong. Please contact admin for support.")
                ]);
            } else {
                $number = $data["number"];
                $type = $data["type"];
                try {
                    /** @var OperatorDataInterface $operatorData */
                    $operatorData = $this->digitalProductRepository->checkPrefix($number);

                    /** @var Page $resultPage */
                    $resultPage = $this->pageFactory->create();

                    /** @var ProductList $block */
                    $block = $resultPage->getLayout()
                        ->createBlock(ProductList::class);

                    if (in_array($type, [
                        CategoryRepository::MOBILE_PACKAGE,
                        Data::MOBILE_PACKAGE_INTERNET_VALUE,
                        Data::MOBILE_PACKAGE_ROAMING_VALUE
                    ])) {
                        $block->setTemplate(self::PRODUCT_LIST_TEMPLATE[CategoryRepository::MOBILE_PACKAGE]);
                        $block->setC1Categories([
                            $this->digitalProductRepository->getProductsByCategory(Data::MOBILE_PACKAGE_INTERNET_VALUE),
                            $this->digitalProductRepository->getProductsByCategory(Data::MOBILE_PACKAGE_ROAMING_VALUE)
                        ]);
                    } else {
                        $block->setTemplate(self::PRODUCT_LIST_TEMPLATE[CategoryRepository::TOPUP]);
                        $block->setC1Categories([
                            $products = $this->digitalProductRepository->getProductsByCategory(Data::TOP_UP_VALUE),
                        ]);
                    }

                    $block
                        ->setCategory($data["category_id"])
                        ->setOperator($operatorData);
                    $jsonData->setData([
                        "status" => 1,
                        "result" => [
                            "operator_icon" => $operatorData->getOperatorIcon(),
                            "product_block" => $block->toHtml()
                        ]
                    ]);
                } catch (LocalizedException $e) {
                    $jsonData->setData([
                        "status" => 0,
                        "message" => __("We cannot find this number. Make sure you enter the correct mobile number")

                    ]);
                    return $jsonData;
                }
            }
        }

        return $jsonData;
    }
}

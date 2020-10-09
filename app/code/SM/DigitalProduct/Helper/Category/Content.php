<?php
/**
 * Class Content
 * @package SM\DigitalProduct\Helper\Category
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace SM\DigitalProduct\Helper\Category;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Store\Model\StoreManagerInterface;
use SM\DigitalProduct\Api\Data\CategoryContentInterface;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Block\Index\HowToBuy;
use SM\DigitalProduct\Helper\Config;
use SM\DigitalProduct\Model\CategoryContent;
use SM\DigitalProduct\Model\ResourceModel\CategoryContent\Collection as CategoryContentCollection;
use \SM\DigitalProduct\Model\CategoryRepository;
use SM\DigitalProduct\Model\ResourceModel\CategoryContent\CollectionFactory as CategoryContentCollectionFactory;
use SM\DigitalProduct\Api\Data\CategoryContentInterfaceFactory;
use \SM\DigitalProduct\Model\DigitalProductRepository;

class Content
{
    /**
     * @var CategoryContentCollectionFactory
     */
    protected $categoryContentCollectionFactory;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var CategoryContentInterfaceFactory
     */
    protected $contentDataFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DigitalProductRepository
     */
    protected $digitalProductRepository;

    /**
     * Content constructor.
     * @param CategoryContentCollectionFactory $categoryContentCollectionFactory
     * @param Config $configHelper
     * @param BlockFactory $blockFactory
     * @param CategoryContentInterfaceFactory $contentDataFactory
     * @param StoreManagerInterface $storeManager
     * @param DigitalProductRepository $digitalProductRepository
     */
    public function __construct(
        CategoryContentCollectionFactory $categoryContentCollectionFactory,
        Config $configHelper,
        BlockFactory $blockFactory,
        CategoryContentInterfaceFactory $contentDataFactory,
        StoreManagerInterface $storeManager,
        DigitalProductRepository $digitalProductRepository
    ) {
        $this->storeManager = $storeManager;
        $this->contentDataFactory = $contentDataFactory;
        $this->blockFactory = $blockFactory;
        $this->configHelper= $configHelper;
        $this->categoryContentCollectionFactory = $categoryContentCollectionFactory;
        $this->digitalProductRepository = $digitalProductRepository;
    }

    /**
     * @param string $digitalCatCode
     * @return mixed|CategoryContentInterface
     */
    public function getCategoryContent($digitalCatCode)
    {
        /** @var CategoryContentCollection $categoryContentCollection */
        $categoryContentCollection = $this->categoryContentCollectionFactory->create();
        $categoryContentCollection->selectWithCategory();
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $storeId = 0;
        }
        $storeIds = ($storeId == 0) ? [0] : [0, $storeId];
        $categoryContentCollection->addFieldToFilter("main_table.store_id", ["in" => $storeIds]);

        if ($digitalCatCode == CategoryRepository::MOBILE_PACKAGE
            || $digitalCatCode == Data::MOBILE_PACKAGE_ROAMING_VALUE) {
            $digitalCatCode = Data::MOBILE_PACKAGE_INTERNET_VALUE;
        }

        $categoryContentCollection->addFieldToFilter("category.type", $digitalCatCode);

        /** @var CategoryContentInterface[] $content */
        $content = $this->generateContent($categoryContentCollection, $storeId, $digitalCatCode);
        return $this->prepareContent($content, $digitalCatCode);
    }

    /**
     * @param CategoryContentCollection $categoryContentCollection
     * @param int $storeId
     * @param $digitalCatCode
     * @return CategoryContentInterface[]
     */
    private function generateContent($categoryContentCollection, $storeId, $digitalCatCode)
    {
        $content = [];

        /** @var CategoryContent $categoryContent */
        foreach ($categoryContentCollection as $categoryContent) {
            $typeId = $categoryContent->getData(CategoryInterface::TYPE);
            if ($digitalCatCode == $typeId) {
                if (isset($content[$typeId])) {
                    if ($storeId != 0) {
                        if ($categoryContent->getData(CategoryContentInterface::STORE_ID) == $storeId) {
                            $content[$typeId] = $categoryContent;
                            continue;
                        }
                    }
                }
                $content[$typeId] = $categoryContent;
                break;
            }
        }

        return $content;
    }

    /**
     * @param $content
     * @param $digitalCatId
     * @return CategoryContentInterface
     */
    private function prepareContent($content, $digitalCatId)
    {
        if ($digitalCatId == CategoryRepository::MOBILE_PACKAGE
            || $digitalCatId == Data::MOBILE_PACKAGE_ROAMING_VALUE) {
            $digitalCatId = Data::MOBILE_PACKAGE_INTERNET_VALUE;
        }

        if (isset($content[$digitalCatId])) {
            $content = $content[$digitalCatId];
        } else {
            $content = $this->createDefaultContent($digitalCatId);
        }
        if (is_null($content->getTooltip())) {
            $content->setTooltip(__(CategoryRepository::DEFAULT_TOOLTIP[$digitalCatId]));
        }
        $content->setCategoryName($this->getCategoryTitle()[$digitalCatId]);
        $content->setType($digitalCatId);
        $catAllowGetProducts = array(Data::ELECTRICITY_TOKEN_VALUE, Data::ELECTRICITY_BILL_VALUE);

        if (in_array($digitalCatId, $catAllowGetProducts)) {
            $content->setProducts($this->digitalProductRepository->getProductsByCategoryMobile($digitalCatId));
            $content->setOperatorImage($this->configHelper->getMediaUrl($this->configHelper->getElectricityOperatorIcon()));
            $content->setOperator(__('PLN Electricity'));
        }
        $content->setHowToBuy($this->getHowToBuyBlockContent($digitalCatId, true));

        return $content;
    }

    /**
     * @param $digitalCatCode
     * @return CategoryContentInterface
     */
    private function createDefaultContent($digitalCatCode)
    {
        /** @var CategoryContentInterface $contentData */
        $contentData = $this->contentDataFactory->create();
        return $contentData->setTooltip(CategoryRepository::DEFAULT_TOOLTIP[$digitalCatCode]);
    }

    /**
     * @param bool $isMobile
     * @return array
     */
    private function getHowToBuyBlock($isMobile = false)
    {
        return [
            Data::TOP_UP_VALUE => $this->configHelper->getTopUpHowToBuyBlockIdentifier($isMobile),
            Data::MOBILE_PACKAGE_INTERNET_VALUE => $this->configHelper->getMobilePackageHowToBuyBlockIdentifier($isMobile),
            Data::ELECTRICITY_BILL_VALUE => $this->configHelper->getElectricityHowToBuyBlockIdentifier($isMobile),
            Data::ELECTRICITY_TOKEN_VALUE => $this->configHelper->getElectricityHowToBuyBlockIdentifier($isMobile)

        ];
    }

    /**
     * @param $digitalCatId
     * @param bool $isMobile
     * @return string
     */
    private function getHowToBuyBlockContent($digitalCatId, $isMobile = false)
    {
        /** @var HowToBuy $howToBuyBlock */
        $howToBuyBlock = $this->blockFactory->createBlock(HowToBuy::class);
        $howToBuyBlock->setTemplate(CategoryRepository::HOW_TO_BUY_TEMPLATE[$digitalCatId]);
        $howToBuyBlock->setHowToBuyBlockIdentifier($this->getHowToBuyBlock($isMobile)[$digitalCatId]);
        $html = $howToBuyBlock->toHtml();
        return $html;
    }

    /**
     * @return array
     */
    private function getCategoryTitle()
    {
        return [
            CategoryRepository::TOPUP => $this->configHelper->getTopUpTitle(),
            CategoryRepository::MOBILE_PACKAGE => $this->configHelper->getMobilePackageTitle(),
            CategoryRepository::ELECTRICITY => $this->configHelper->getElectricityTitle(),
            Data::ELECTRICITY_BILL_VALUE => __(CategoryRepository::ELECTRICITY_BILL_TITLE),
            Data::ELECTRICITY_TOKEN_VALUE => __(CategoryRepository::ELECTRICITY_TOKEN_TITLE),
            Data::MOBILE_PACKAGE_INTERNET_VALUE => __(CategoryRepository::MOBILE_PACKAGE_INTERNET_TITLE),
            Data::MOBILE_PACKAGE_ROAMING_VALUE => __(CategoryRepository::MOBILE_PACKAGE_ROAMING_TITLE)
        ];
    }
}

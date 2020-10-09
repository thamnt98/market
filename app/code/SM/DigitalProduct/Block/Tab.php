<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Block
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Block;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Api\Data\CategoryContentInterface;
use SM\DigitalProduct\Helper\Config;
use SM\DigitalProduct\Model\CategoryRepository;

/**
 * Class Tab
 * @package SM\DigitalProduct\Block
 */
class Tab extends Template
{
    const ACTIVE = "active";


    /**
     * @var string
     */
    private $tab;
    /**
     * @var CategoryRepository
     */
    protected $digitalCategoryRepository;

    protected $configHelper;
    private $types;
    private $categories;

    /**
     * Tab constructor.
     * @param Template\Context $context
     * @param CategoryRepository $digitalCategoryRepository
     * @param Config $configHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CategoryRepository $digitalCategoryRepository,
        Config $configHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->types = [];
        $this->digitalCategoryRepository = $digitalCategoryRepository;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->generateCategories();
        return parent::_prepareLayout(); // TODO: Change the autogenerated stub
    }

    public function generateCategories()
    {
        try {
            /** @var CategoryInterface[] $categories */
            $categories = $this->digitalCategoryRepository->getList();

            $types = [];
            foreach ($categories as $category) {
                if ($this->getCategoryCode() == $category->getType()) {
                    $this->pageConfig->getTitle()->set(__($category->getCategoryName()));
                }
                $typeMap = json_decode($category->getType(), true);
                if (is_array($typeMap)) {
                    foreach ($typeMap as $record) {
                        if (isset($record["type"])) {
                            $types[] = $record["type"];
                        }
                    }
                }
            }
            $this->setTypes($types);
            $this->setCategories($categories);
        } catch (LocalizedException|NoSuchEntityException $e) {
            $this->setCategories([]);
        }
    }

    /**
     * @return CategoryInterface[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param CategoryInterface[] $categories
     * @return Tab
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return string
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * @return string
     */
    public function getCategoryCode()
    {
        return $this->getRequest()->getControllerName();
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setTab($value)
    {
        $this->tab = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     * @return Tab
     */
    public function setTypes($types)
    {
        $this->types = $types;
        return $this;
    }

    /**
     * @param string $digitalCatType
     * @return string
     */
    public function getTitle($digitalCatType)
    {
        switch ($digitalCatType) {
            case CategoryRepository::TOPUP:
                return $this->configHelper->getTopUpTitle();
            case CategoryRepository::MOBILE_PACKAGE:
                return $this->configHelper->getMobilePackageTitle();
            case CategoryRepository::ELECTRICITY:
                return $this->configHelper->getElectricityTitle();
            default:
                return "";
        }
    }

    /**
     * @param string $digitalCatType
     * @return int
     */
    public function isActive($digitalCatType)
    {
        switch ($digitalCatType) {
            case CategoryRepository::TOPUP:
                return $this->configHelper->isActiveTopUp();
            case CategoryRepository::MOBILE_PACKAGE:
                return $this->configHelper->isActiveMobilePackage();
            case CategoryRepository::ELECTRICITY:
                return $this->configHelper->isActiveElectricity();
            default:
                return 0;
        }
    }

    /**
     * @param string $digitalCatType
     * @return string
     */
    public function getThumbnail($digitalCatType)
    {
        switch ($digitalCatType) {
            case CategoryRepository::TOPUP:
                return (is_null($this->configHelper->getTopUpThumbnail()) ?
                    $this->defaultThumbnail(CategoryRepository::TOPUP) :
                    $this->configHelper->getTopUpThumbnail());
            case CategoryRepository::MOBILE_PACKAGE:
                return (is_null($this->configHelper->getMobilePackageThumbnail()) ?
                    $this->defaultThumbnail(CategoryRepository::MOBILE_PACKAGE) :
                    $this->configHelper->getMobilePackageThumbnail());
            case CategoryRepository::ELECTRICITY:
                return (is_null($this->configHelper->getElectricityThumbnail()) ?
                    $this->defaultThumbnail(CategoryRepository::ELECTRICITY) :
                    $this->configHelper->getElectricityThumbnail());
            default:
                return "";
        }
    }

    /**
     * @param CategoryInterface $category
     * @return string
     */
    public function getCategoryImage($category)
    {
        if (!is_null($category->getThumbnail())) {
            $thumbnailPart = json_decode($category->getThumbnail(), true);
            if (isset($thumbnailPart["url"])) {
                return $thumbnailPart["url"];
            }
        }
        return $this->defaultThumbnail($category->getExtensionAttributes()->getCategoryCode());
    }
}

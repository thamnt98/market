<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Helper
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\DigitalProduct\Model\CategoryRepository;
use SM\DigitalProduct\Model\Config\Backend\Image;

/**
 * Class Config
 * @package SM\DigitalProduct\Helper
 */
class Config
{
    const TOP_UP_THUMBNAIL = "images/svg-icons/digital-product-icons/top-up.png";
    const MOBILE_PACKAGE_THUMBNAIL = "images/svg-icons/digital-product-icons/mobile-package.png";
    const ELECTRICITY_THUMBNAIL = "images/svg-icons/digital-product-icons/pln-elec.png";
    const PDAM_THUMBNAIL = "images/svg-icons/digital-product-icons/pdamwater.png";
    const MOBILE_POSTPAID_THUMBNAIL = "images/svg-icons/digital-product-icons/mobile-post.png";
    const BPJS_THUMBNAIL = "images/svg-icons/digital-product-icons/bpjs.png";
    const TELKOM_THUMBNAIL = "images/svg-icons/digital-product-icons/telkom.png";

    const XML_BANNER_IDENTIFIER = "digital_product/general/cms_content";
    const XML_C0_CATEGORY = "digital_product/general/c0_category";
    const XML_PATH_HOME_PAGE = "digital_product/general/home_page";

    const XML_TOP_UP_IS_ACTIVE = "digital_product/topup/is_active";
    const XML_TOP_UP_TITLE = "digital_product/topup/title";
    const XML_TOP_UP_THUMBNAIL = "digital_product/topup/image";
    const XML_TOP_UP_HOW_TO_BUY_BLOCK_IDENTIFIER = "digital_product/topup/how_to_buy_block";

    const XML_MOBILEPACKAGE_IS_ACTIVE = "digital_product/mobilepackage/is_active";
    const XML_MOBILEPACKAGE_TITLE = "digital_product/mobilepackage/title";
    const XML_MOBILEPACKAGE_THUMBNAIL = "digital_product/mobilepackage/image";
    const XML_MOBILEPACKAGE_HOW_TO_BUY_BLOCK_IDENTIFIER = "digital_product/mobilepackage/how_to_buy_block";
    const XML_BLOCK_IDENTIFIER_MOBILE_SUFFIX = "_mb";

    const XML_ELECTRICITY_IS_ACTIVE = "digital_product/electricity/is_active";
    const XML_ELECTRICITY_TITLE = "digital_product/electricity/title";
    const XML_ELECTRICITY_THUMBNAIL = "digital_product/electricity/image";
    const XML_ELECTRICITY_OPERATOR = "digital_product/electricity/operator_icon";
    const XML_ELECTRICITY_HOW_TO_BUY_BLOCK_IDENTIFIER = "digital_product/electricity/how_to_buy_block";
    const XML_ELECTRICITY_BILL_HOW_TO_BUY_BLOCK_IDENTIFIER = "digital_product/electricity/how_to_buy_block_bill";

    const XML_QUICK_TRANSACTION_MAX_TRANSACTION = "digital_product/quick_transaction/limit";
    const XML_ENABLE_DIGITAL = "digital_product/general/enable";

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Asset service
     *
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Repository $assetRepo
     * @param StoreManagerInterface $storeManager
     * @param Emulation $emulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Repository $assetRepo,
        StoreManagerInterface $storeManager,
        Emulation $emulation
    ) {
        $this->emulation = $emulation;
        $this->storeManager = $storeManager;
        $this->assetRepo = $assetRepo;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return int
     */
    public function getC0CategoryId()
    {
        return $this->scopeConfig->getValue(
            self::XML_C0_CATEGORY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getHomePageType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HOME_PAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getBannerBlockIdentifier()
    {
        return $this->scopeConfig->getValue(
            self::XML_BANNER_IDENTIFIER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function isActiveTopUp()
    {
        return $this->scopeConfig->getValue(
            self::XML_TOP_UP_IS_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getTopUpTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_TOP_UP_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getTopUpThumbnail()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_TOP_UP_THUMBNAIL,
            ScopeInterface::SCOPE_STORE
        );

        try {
            return (is_null($value) ?
                $this->defaultThumbnail(CategoryRepository::TOPUP) :
                $this->getMediaUrl($value));
        } catch (NoSuchEntityException $e) {
            return $this->defaultThumbnail(CategoryRepository::TOPUP);
        }
    }

    /**
     * @param $isMobile
     * @return string
     */
    public function getTopUpHowToBuyBlockIdentifier($isMobile)
    {
        $path = $isMobile ? self::XML_TOP_UP_HOW_TO_BUY_BLOCK_IDENTIFIER . self::XML_BLOCK_IDENTIFIER_MOBILE_SUFFIX
            : self::XML_TOP_UP_HOW_TO_BUY_BLOCK_IDENTIFIER;
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function isActiveMobilePackage()
    {
        return $this->scopeConfig->getValue(
            self::XML_MOBILEPACKAGE_IS_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getMobilePackageTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_MOBILEPACKAGE_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getMobilePackageThumbnail()
    {
        $value =  $this->scopeConfig->getValue(
            self::XML_MOBILEPACKAGE_THUMBNAIL,
            ScopeInterface::SCOPE_STORE
        );

        try {
            return (is_null($value) ?
                $this->defaultThumbnail(CategoryRepository::MOBILE_PACKAGE) :
                $this->getMediaUrl($value));
        } catch (NoSuchEntityException $e) {
            return $this->defaultThumbnail(CategoryRepository::MOBILE_PACKAGE);
        }
    }

    /**
     * @param $isMobile
     * @return string
     */
    public function getMobilePackageHowToBuyBlockIdentifier($isMobile)
    {
        $path = $isMobile ? self::XML_MOBILEPACKAGE_HOW_TO_BUY_BLOCK_IDENTIFIER . self::XML_BLOCK_IDENTIFIER_MOBILE_SUFFIX
            : self::XML_MOBILEPACKAGE_HOW_TO_BUY_BLOCK_IDENTIFIER;
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function isActiveElectricity()
    {
        return $this->scopeConfig->getValue(
            self::XML_ELECTRICITY_IS_ACTIVE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getElectricityTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_ELECTRICITY_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getElectricityThumbnail()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_ELECTRICITY_THUMBNAIL,
            ScopeInterface::SCOPE_STORE
        );

        try {
            return (is_null($value) ?
                $this->defaultThumbnail(CategoryRepository::ELECTRICITY) :
                $this->getMediaUrl($value));
        } catch (NoSuchEntityException $e) {
            return $this->defaultThumbnail(CategoryRepository::ELECTRICITY);
        }
    }

    /**
     * @return mixed
     */
    public function getElectricityOperatorIcon()
    {
         return $this->scopeConfig->getValue(
             self::XML_ELECTRICITY_OPERATOR,
             ScopeInterface::SCOPE_STORE
         );
    }

    /**
     * @param $isMobile
     * @return string
     */
    public function getElectricityHowToBuyBlockIdentifier($isMobile)
    {
        $path = $isMobile ? self::XML_ELECTRICITY_HOW_TO_BUY_BLOCK_IDENTIFIER . self::XML_BLOCK_IDENTIFIER_MOBILE_SUFFIX
            : self::XML_ELECTRICITY_HOW_TO_BUY_BLOCK_IDENTIFIER;
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $isMobile
     * @return mixed
     */
    public function getElectricityBillHowToBuyBlockIdentifier($isMobile)
    {
        $path = $isMobile ? self::XML_ELECTRICITY_BILL_HOW_TO_BUY_BLOCK_IDENTIFIER . self::XML_BLOCK_IDENTIFIER_MOBILE_SUFFIX
            : self::XML_ELECTRICITY_BILL_HOW_TO_BUY_BLOCK_IDENTIFIER;
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $code
     * @return string
     */
    public function defaultThumbnail($code)
    {
        switch ($code) {
            case CategoryRepository::MOBILE_POSTPAID:
                return $this->getViewFileUrl(self::MOBILE_POSTPAID_THUMBNAIL);
            case CategoryRepository::MOBILE_PACKAGE:
                return $this->getViewFileUrl(self::MOBILE_PACKAGE_THUMBNAIL);
            case CategoryRepository::TELKOM:
                return $this->getViewFileUrl(self::TELKOM_THUMBNAIL);
            case CategoryRepository::PDAM_WATER:
                return $this->getViewFileUrl(self::PDAM_THUMBNAIL);
            case CategoryRepository::BPJS:
                return $this->getViewFileUrl(self::BPJS_THUMBNAIL);
            case CategoryRepository::ELECTRICITY:
                return $this->getViewFileUrl(self::ELECTRICITY_THUMBNAIL);
            default:
                return $this->getViewFileUrl(self::TOP_UP_THUMBNAIL);
        }
    }

    /**
     * Retrieve url of a view file
     *
     * @param string $fileId
     * @param array $params
     * @return string
     */
    public function getViewFileUrl($fileId, array $params = [])
    {
        try {
            $this->emulation->startEnvironmentEmulation(
                $this->storeManager->getStore()->getId(),
                Area::AREA_FRONTEND,
                true
            );
            $url = $this->assetRepo->getUrlWithParams($fileId, $params);
            $this->emulation->stopEnvironmentEmulation();
            return $url;
        } catch (NoSuchEntityException $e) {
            return $this->assetRepo->getUrlWithParams($fileId, $params);
        }
    }

    /**
     * @param string $value
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getMediaUrl($value)
    {
        return $this->storeManager->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . Image::UPLOAD_DIR . "/" . $value;
    }

    /**
     * @return int
     */
    public function getMaxNumberTransactionToShow()
    {
        return $this->scopeConfig->getValue(
            self::XML_QUICK_TRANSACTION_MAX_TRANSACTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_ENABLE_DIGITAL);
    }
}

<?php
/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Plugin\Controller;

use Amasty\ShopbyBrand\Controller\Index\Index as Subject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\PageFactory;
use Amasty\ShopbyBase\Api\Data\OptionSettingRepositoryInterface;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as OptionCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Theme\Block\Html\Breadcrumbs;

/**
 * Class IndexPlugin
 */
class Index
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var OptionSettingRepositoryInterface
     */
    protected $optionSettingRepository;

    /**
     * @var OptionCollectionFactory
     */
    protected $optionCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    protected $pageConfig;
    protected $pageTitle;

    /**
     *
     * @param PageFactory $resultPageFactory
     * @param OptionSettingRepositoryInterface $optionSettingRepository
     * @param OptionCollectionFactory $optionCollectionFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $pageConfig
     * @param Title $pageTitle
     */
    public function __construct(
        PageFactory $resultPageFactory,
        OptionSettingRepositoryInterface $optionSettingRepository,
        OptionCollectionFactory $optionCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        Config $pageConfig,
        Title $pageTitle
    ) {
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->optionSettingRepository = $optionSettingRepository;
        $this->resultPageFactory       = $resultPageFactory;
        $this->scopeConfig             = $scopeConfig;
        $this->pageConfig             = $pageConfig;
        $this->pageTitle = $pageTitle;
    }


    /**
     * @param Subject $subject
     * @param $result
     * @return $this
     */
    public function afterExecute(Subject $subject, $result)
    {
        try {
            $value = $subject->getRequest()->getParam($this->getBrandAttributeCode());
            $sideBar = 'div.sidebar.main';
            $blockSub = 'landing.content.subcategories';
            $blockProduct = 'category.products.list';

            $page = $this->resultPageFactory->create();

            $collection = $this->optionCollectionFactory->create();
            $collection->addFieldToFilter('value', $value);
            $data = $collection->getData();
            $title = $data[0]['title'];

            $listLayout = array('1column','empty','cms-full-width','product-full-width');
            if ($data[0]['brand_page_layout']) {
                $layout = $data[0]['brand_page_layout'];

                if (in_array($layout, $listLayout)) {
                    $this->setLayout($page, $sideBar, $blockSub, $blockProduct);
                } else {
                    $this->setLayout($page, $sideBar = null, $blockSub, $blockProduct);
                }

                $page->getConfig()->setPageLayout($layout);

                /** @var Breadcrumbs $breadcrumbsBlock */
                if ($breadcrumbsBlock = $page->getLayout()->getBlock('breadcrumbs')) {
                    if ($title != null) {
                        $this->pageConfig->getTitle()->set(__($title));
                        $this->addCrumb($breadcrumbsBlock, $title);
                    } else {
                        $defaultTitle = $this->pageTitle->getShort();
                        $this->addCrumb($breadcrumbsBlock, $defaultTitle);
                    }
                }
            }
        } catch (\Exception $e) {
        }
        return $result;
    }

    /**
     * @param $page
     * @param $sideBar
     * @param $blockProduct
     * @param $blockSub
     * @return $this
     */
    protected function setLayout($page, $sideBar, $blockProduct, $blockSub)
    {
        return $page->getLayout()
            ->unsetElement($sideBar)
            ->unsetElement($blockProduct)
            ->unsetElement($blockSub);
    }

    /**
     * @return string
     */
    protected function getBrandAttributeCode()
    {
        return $this->scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $breadcrumbsBlock
     * @param $title
     * @return mixed
     */
    protected function addCrumb($breadcrumbsBlock, $title)
    {
        return $breadcrumbsBlock->addCrumb(
            'brand',
            [
                'label' => __($title),
                'title' => __($title),
            ]
        );
    }
}

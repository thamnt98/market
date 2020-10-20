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

namespace SM\TodayDeal\Block\Widget;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use SM\TodayDeal\Model\Post;
use SM\TodayDeal\Model\ResourceModel\Post\Collection;
use SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory;
use SM\TodayDeal\Api\Data\PostInterface;
use SM\TodayDeal\Helper\HelperImage;

/**
 * Class LatestPromo
 * @package SM\MPBlog\Block\Widget
 */
class LatestPromo extends Template implements BlockInterface
{
    protected $_template = "widget/latest-promo.phtml";


    /**
     * @var \SM\GTM\Helper\Data
     */
    protected $gtmHelper;

    /**
     * @var Collection
     */
    private $postCollection;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var AdapterFactory
     */
    private $imageFactory;

    /**
     * @var HelperImage
     */
    protected $helper;

    /**
     * LatestPromo constructor.
     *
     * @param \SM\GTM\Helper\Data $gtmHelper
     * @param Template\Context $context
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param CollectionFactory $postCollectionFactory
     * @param HelperImage $helper
     * @param array $data
     */
    public function __construct(
        \SM\GTM\Helper\Data $gtmHelper,
        Template\Context $context,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        CollectionFactory $postCollectionFactory,
        HelperImage $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->postCollection = $postCollectionFactory->create();
        $this->filesystem     = $filesystem;
        $this->imageFactory   = $imageFactory;
        $this->gtmHelper      = $gtmHelper;
        $this->helper         = $helper;
    }

    /**
     * Get Latest Promo Topics
     * @return Collection
     */
    public function getPost()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->postCollection->addFieldToFilter(PostInterface::IS_ACTIVE, '1')
            ->addStoreFilter($storeId)
            ->setOrder(PostInterface::SORT_ORDER, 'asc')
            ->setPageSize(6);
    }


    /**
     * Get See all Url
     * @return string
     * @throws NoSuchEntityException
     */
    public function getSeeAllUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . 'todaydeal';
    }

    /**
     * @param $image
     * @param $path
     * @param null $width
     * @param null $height
     * @return bool|string
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function getImageResize($image, $path, $width = null, $height = null)
    {
        return $this->helper->getImageResize($image, $path, $width, $height);
    }

    /**
     * @param \SM\TodayDeal\Model\Post $dealItem
     *
     * @return string
     */
    public function getGtm($dealItem)
    {
        return $this->getGtmHelper()->prepareLatestDealData($dealItem);
    }

    public function getGtmHelper()
    {
        return $this->gtmHelper;
    }
}

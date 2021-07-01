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

namespace SM\TodayDeal\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\View\Element\Template\Context;
use SM\TodayDeal\Api\Data\PostInterface;
use SM\TodayDeal\Helper\HelperImage;
use SM\TodayDeal\Model\ResourceModel\Post\Collection;
use SM\TodayDeal\Model\ResourceModel\Post\Collection as PostCollection;
use SM\TodayDeal\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use SM\TodayDeal\Model\Post;

class PostList extends \Magento\Framework\View\Element\Template
{
    const PAGING = 'todaydeal/paging/paging_value';
    /**
     * Thumbnail path
     */
    const POST_PATH = 'todaydeal/post';

    /**
     * @var PostCollection
     */
    protected $postCollectionFactory = null;

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
     * Constructor
     *
     * @param Context $context
     * @param PostCollectionFactory $postCollectionFactory
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param HelperImage $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostCollectionFactory $postCollectionFactory,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        HelperImage $helper,
        array $data = []
    ) {
        $this->postCollectionFactory = $postCollectionFactory->create();
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->helper       = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Golbal Prepare Layout
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set(__("Today's Deals"));
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager');
        $pager->setAvailableLimit([$this->getPagingConfig() => $this->getPagingConfig()]);
        $pager->setShowPerPage($this->getPagingConfig());
        $pager->setCollection($this->getPosts());
        $this->setChild("posts.pager", $pager);
        parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');

        $breadcrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->_storeManager->getStore()->getBaseUrl()
            ]
        );
        $breadcrumbsBlock->addCrumb('today_deals', ['label' => __("Curated For You"), 'title' => __("Curated For You")]);
    }

    /**
     * Get Today Deals Post
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getPosts()
    {
        $request = $this->getRequest();
        $page = ($request->getParam('p')) ? $request->getParam('p') : 1;
        $limit = ($request->getParam('limit')) ? $request->getParam('limit') : $this->getPagingConfig();
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->postCollectionFactory->addfieldToFilter(PostInterface::IS_ACTIVE, "1")
            ->addStoreFilter($storeId)
            ->setOrder(PostInterface::SORT_ORDER, 'asc')
            ->setPageSize($limit)
            ->setCurPage($page);
    }

    /**
     * @return mixed
     */
    public function getPagingConfig()
    {
        return $this->_scopeConfig->getValue(self::PAGING);
    }

    /**
     * @param $image
     * @param $path
     * @param null $width
     * @param null $height
     * @return bool|string
     * @throws \Exception
     */
    public function getImageResize($image, $path, $width = null, $height = null)
    {
        return $this->helper->getImageResize($image, $path, $width, $height);
    }
}

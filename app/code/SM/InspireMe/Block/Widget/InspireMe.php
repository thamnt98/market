<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Widget;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Mirasvit\Blog\Api\Data\PostInterface;
use Mirasvit\Blog\Model\Config;
use Mirasvit\Blog\Model\ResourceModel\Post\Collection;
use Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory;
use SM\InspireMe\Helper\Data;

/**
 * Class InspireMe
 * @package SM\InspireMe\Block\Widget
 */
class InspireMe extends Template implements BlockInterface
{
    protected $_template = "widget/inspire-me.phtml";

    const POST_POSITION = 'position';

    /**
     * @var Collection
     */
    protected $postCollection;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var CollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * InspireMe constructor.
     * @param Template\Context $context
     * @param CollectionFactory $postCollectionFactory
     * @param Data $dataHelper
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $postCollectionFactory,
        Data $dataHelper,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->config = $config;
    }

    /**
     * @return Collection
     */
    public function getPostCollection()
    {
        $positionConfig = $this->dataHelper->getHomepagePositionConfig();
        if (!$this->postCollection) {
            switch ($positionConfig) {
                case $this->dataHelper::CONFIG_POSITION_MOST_VIEW: {
                    $this->postCollection = $this->postCollectionFactory->create()
                        ->addVisibilityFilter()
                        ->setOrder('views_count', 'DESC')
                        ->setPageSize(5);
                    break;
                }
                case $this->dataHelper::CONFIG_POSITION_RECENT_UPLOAD: {
                    $this->postCollection = $this->postCollectionFactory->create()
                        ->addVisibilityFilter()
                        ->setOrder(PostInterface::CREATED_AT, 'DESC')
                        ->setPageSize(5);
                    break;
                }
                default: {
                    $this->postCollection = $this->postCollectionFactory->create()
                        ->addVisibilityFilter()
                        ->setOrder(self::POST_POSITION, 'DESC')
                        ->setPageSize(5);
                }
            }
        }
        return $this->postCollection;
    }

    /**
     * Get See all Url
     * @return string
     */
    public function getSeeAllUrl()
    {
        return $this->config->getBaseUrl();
    }

    /**
     * @param $image
     * @param null $width
     * @param null $height
     * @return bool|string
     * @throws NoSuchEntityException
     */
    public function getImageResize($image, $width = null, $height = null)
    {
        return $this->dataHelper->getImageResize($image, $width, $height);
    }

    /**
     * @param $post
     *
     * @return string
     */
    public function getGtm($post)
    {
        return $this->dataHelper->prepareGtmData($post);
    }

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     * @return string
     */
    public function getHomeImageUrl($post)
    {
        return $this->config->getMediaUrl($post->getData(\SM\InspireMe\Helper\Data::POST_DATA_HOME_IMAGE));
    }
}

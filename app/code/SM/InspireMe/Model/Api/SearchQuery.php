<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_InspireMe
 *
 * Date: April, 17 2020
 * Time: 2:26 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\InspireMe\Model\Api;

use SM\InspireMe\Model\Data\PostListing;

class SearchQuery implements \SM\InspireMe\Api\SearchQueryInterface
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Helper
     */
    protected $resourceHelper;

    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory
     */
    protected $postCollFact;

    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $postTopicCollFact;

    /**
     * @var \SM\InspireMe\Model\Data\PostListingFactory
     */
    protected $postFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * SearchQuery constructor.
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \SM\InspireMe\Model\Data\PostListingFactory $postFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $eavHelper
     * @param \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory $postCollFact
     * @param \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $postTopicCollFact
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \SM\InspireMe\Model\Data\PostListingFactory $postFactory,
        \Magento\Eav\Model\ResourceModel\Helper $eavHelper,
        \Mirasvit\Blog\Model\ResourceModel\Post\CollectionFactory $postCollFact,
        \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $postTopicCollFact
    ) {
        $this->resourceHelper = $eavHelper;
        $this->postCollFact = $postCollFact;
        $this->postTopicCollFact = $postTopicCollFact;
        $this->postFactory = $postFactory;
        $this->timezone = $timezone;
    }

    /**
     * @param string $str
     *
     * @return \SM\InspireMe\Api\Data\PostListingInterface[]
     * @throws \Exception
     */
    public function query($str)
    {
        $result = [];
        /** @var \Mirasvit\Blog\Model\ResourceModel\Post\Collection $coll */
        $coll = $this->postCollFact->create();
        $coll->addSearchFilter($str);

        /** @var \Mirasvit\Blog\Api\Data\PostInterface $post */
        foreach ($coll->getItems() as $post) {
            if ($post->getStatus() != \Mirasvit\Blog\Api\Data\PostInterface::STATUS_PUBLISHED) {
                continue;
            }
            /** @var \SM\InspireMe\Model\Data\PostListing $data */
            $data = $this->postFactory->create();
            $data->setId($post->getEntityId());
            $data->setType($post->getType());
            $data->setName($post->getName());
            $data->setShortContent($post->getShortContent());
            $data->setPublishedDate($this->_formatDate($post->getPublishedDate()));
            $data->setHomeImage($post->getFeaturedImageUrl());
            $result[] = $data;
        }

        return $result;
    }

    /**
     * @param string $date
     * @return string
     * @throws \Exception
     */
    public function _formatDate($date)
    {
        return $this->timezone->date(new \DateTime($date))->format('d F Y');
    }
}

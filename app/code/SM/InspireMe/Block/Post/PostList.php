<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Post;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Blog\Api\Data\PostInterface;
use Mirasvit\Blog\Api\Repository\PostRepositoryInterface;
use Mirasvit\Blog\Block\Post\PostList\Toolbar;
use Mirasvit\Blog\Model\Config;
use Mirasvit\Blog\Model\ResourceModel\Post\Collection;

/**
 * Class PostList
 * @package SM\InspireMe\Block\Post
 */
class PostList extends \Mirasvit\Blog\Block\Post\PostList
{
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * PostList constructor.
     * @param PostRepositoryInterface $postRepository
     * @param Config $config
     * @param Registry $registry
     * @param TimezoneInterface $timezone
     * @param Context $context
     */
    public function __construct(
        PostRepositoryInterface $postRepository,
        Config $config,
        Registry $registry,
        TimezoneInterface $timezone,
        Context $context
    ) {
        parent::__construct($postRepository, $config, $registry, $context);
        $this->postRepository = $postRepository;
        $this->timezone = $timezone;
    }

    /**
     * @return \Mirasvit\Blog\Block\Post\PostList
     * @throws LocalizedException
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getPostCollection();

        $this->addToolbarBlock($collection);

        if (!$collection->isLoaded()) {
            $collection->load();
        }

        return $this;
    }

    /**
     * Get toolbar block from layout
     *
     * @return bool|Toolbar
     * @throws LocalizedException
     */
    private function getToolbarFromLayout()
    {
        $blockName = $this->getToolbarBlockName();

        $toolbarLayout = false;

        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }

        return $toolbarLayout;
    }

    /**
     * Retrieve Toolbar block from layout or a default Toolbar
     *
     * @return bool|BlockInterface|Toolbar
     * @throws LocalizedException
     */
    public function getToolbarBlock()
    {
        $block = $this->getToolbarFromLayout();

        if (!$block) {
            $block = $this->getLayout()->createBlock($this->defaultToolbarBlock, uniqid(microtime()));
        }

        return $block;
    }

    /**
     * Add toolbar block from post listing layout
     *
     * @param Collection $collection
     * @throws LocalizedException
     */
    private function addToolbarBlock(Collection $collection)
    {
        $toolbarLayout = $this->getToolbarFromLayout();

        if ($toolbarLayout) {
            $this->configureToolbar($toolbarLayout, $collection);
        }
    }

    /**
     * Configures the Toolbar block with options from this block and configured product collection.
     *
     * The purpose of this method is the one-way sharing of different sorting related data
     * between this block, which is responsible for product list rendering,
     * and the Toolbar block, whose responsibility is a rendering of these options.
     * @param Toolbar $toolbar
     * @param Collection $collection
     */
    private function configureToolbar(Toolbar $toolbar, Collection $collection)
    {
        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }

    /**
     * @return PostInterface[]|Collection
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getPostCollection()
    {
        $toolbar = $this->getToolbarBlock();

        if (empty($this->collection)) {
            $collection = $this->postRepository->getCollection()
                ->addAttributeToSelect([
                    'name', 'featured_image', 'featured_alt', 'featured_show_on_home',
                    'short_content', 'content', 'url_key',
                ])
                ->addStoreFilter($this->context->getStoreManager()->getStore()->getId())
                ->addVisibilityFilter();

            if ($category = $this->getCategory()) {
                $collection->addCategoryFilter($category);
            } elseif ($tag = $this->getTag()) {
                $collection->addTagFilter($tag);
            } elseif ($author = $this->getAuthor()) {
                $collection->addAuthorFilter($author);
            } elseif ($q = $this->getRequest()->getParam('q')) {
                $collection->addSearchFilter($q);
            }

            $collection->setCurPage($this->getCurrentPage());

            $limit = (int)$toolbar->getLimit();
            if ($limit) {
                $collection->setPageSize($limit);
            }

            $page = (int)$toolbar->getCurrentPage();
            if ($page) {
                $collection->setCurPage($page);
            }

            if ($order = $toolbar->getCurrentOrder()) {
                $collection->setOrder($order, $toolbar->getCurrentDirection());
            }

            $this->collection = $collection;
        }

        return $this->collection;
    }

    /**
     * @param $date
     * @return string
     */
    public function getFormatDate($date)
    {
        return $this->timezone->formatDateTime(
            $date,
            null,
            null,
            null,
            $this->timezone->getConfigTimezone(),
            'd LLL YYYY'
        );
    }
}

<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Post\View;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Mirasvit\Blog\Api\Data\PostInterface;
use Mirasvit\Blog\Api\Repository\PostRepositoryInterface;
use Mirasvit\Blog\Model\Config;
use Mirasvit\Blog\Model\Post;
use Mirasvit\Blog\Model\ResourceModel\Post\Collection as PostCollection;
use SM\InspireMe\Helper\Data;

/**
 * Class MostPopular
 * @package SM\InspireMe\Block\Post\View
 */
class MostPopular extends Template
{
    const VIEWS_COUNT = 'views_count';

    /**
     * @var PostCollection
     */
    protected $postCollection;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var Config
     */
    protected $config;

    /**
     * MostPopular constructor.
     * @param Template\Context $context
     * @param PostRepositoryInterface $postRepository
     * @param Registry $registry
     * @param Data $dataHelper
     * @param TimezoneInterface $timezone
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PostRepositoryInterface $postRepository,
        Registry $registry,
        Data $dataHelper,
        TimezoneInterface $timezone,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->dataHelper = $dataHelper;
        $this->postRepository = $postRepository;
        $this->timezone = $timezone;
        $this->config = $config;
    }

    /**
     * @return array
     */
    protected function getConfigPosts()
    {
        $result = [];
        $config = $this->dataHelper->getMostPopularConfig();

        foreach ($config as $item) {
            if ((int)$item[Data::MP_BASED_ON]) {
                try {
                    $result[] = $this->postRepository->get((int)$item[Data::MP_SELECT_ARTICLE_ID]);
                } catch (\Exception $e) {
                    $result[] = null;
                }
            } else {
                $result[] = null;
            }
        }

        return $result;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getMostPopularPosts()
    {
        $result = $this->getConfigPosts();

        $selectedId[] = $this->getCurrentPost()->getId();
        foreach ($result as $item) {
            if ($item) {
                $selectedId[] = $item->getId();
            }
        }

        $this->postCollection = $this->postRepository->getCollection()
            ->addFieldToFilter(PostInterface::ID, ['nin' => $selectedId])
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addVisibilityFilter()
            ->setOrder(self::VIEWS_COUNT, 'DESC')
            ->setPageSize(3);

        $itemArray = array_values($this->postCollection->getItems());
        $collectionSize = count($itemArray);
        $collectionSelect = 0;

        foreach ($result as &$item) {
            if (!$item && $collectionSelect < $collectionSize) {
                if (isset($itemArray[$collectionSelect])) {
                    $item = $itemArray[$collectionSelect];
                }
                $collectionSelect++;
            }
        }

        return $result;
    }

    /**
     * @return Post
     */
    public function getCurrentPost()
    {
        return $this->registry->registry('current_blog_post');
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

    /**
     * Get See all Url
     * @return string
     */
    public function getSeeAllUrl()
    {
        return $this->config->getBaseUrl();
    }
}

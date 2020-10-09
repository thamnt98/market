<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Ui\Post\Form;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Mirasvit\Blog\Api\Data\PostInterface;
use Mirasvit\Blog\Api\Repository\PostRepositoryInterface;
use Mirasvit\Blog\Model\Config;
use SM\InspireMe\Helper\Data;

/**
 * Class DataProvider
 * @package SM\InspireMe\Ui\Post\Form
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const POST_POSITION = 'position';
    const POST_LOOK_BOOK_ID = 'look_book_id';
    const POST_SHOW_HOT_SPOT = 'show_hot_spot';
    const IS_SHORT_CONTENT = 'is_short_content';
    const POSITION_HOMEPAGE = 'position_homepage';

    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * DataProvider constructor.
     * @param PostRepositoryInterface $postRepository
     * @param Config $config
     * @param Status $status
     * @param ImageHelper $imageHelper
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        PostRepositoryInterface $postRepository,
        Config $config,
        Status $status,
        ImageHelper $imageHelper,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->postRepository = $postRepository;
        $this->collection     = $this->postRepository->getCollection();
        $this->config         = $config;
        $this->status         = $status;
        $this->imageHelper    = $imageHelper;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        /** @var \Mirasvit\Blog\Model\Post $post */
        foreach ($this->collection as $post) {
            if ($post && $post->getId()) {
                $result[$post->getId()] = [
                    PostInterface::ID               => $post->getId(),
                    PostInterface::STATUS           => $post->getStatus(),
                    PostInterface::CREATED_AT       => $post->getCreatedAt(),
                    PostInterface::IS_PINNED        => $post->isPinned(),
                    PostInterface::AUTHOR_ID        => $post->getAuthorId(),
                    PostInterface::NAME             => $post->getName(),
                    PostInterface::SHORT_CONTENT    => $post->getShortContent(),
                    PostInterface::CONTENT          => $post->getContent(),
                    PostInterface::URL_KEY          => $post->getUrlKey(),
                    PostInterface::META_TITLE       => $post->getMetaTitle(),
                    PostInterface::META_KEYWORDS    => $post->getMetaKeywords(),
                    PostInterface::META_DESCRIPTION => $post->getMetaDescription(),
                    PostInterface::CATEGORY_IDS     => $post->getCategoryIds(),
                    PostInterface::STORE_IDS        => $post->getStoreIds(),
                    PostInterface::TAG_IDS          => $post->getTagIds(),
                    self::POST_LOOK_BOOK_ID         => $post->getData(self::POST_LOOK_BOOK_ID),
                    self::POST_SHOW_HOT_SPOT        => $post->getData(self::POST_SHOW_HOT_SPOT),
                    Data::RELATED_PRODUCT_TITLE     => $post->getData(Data::RELATED_PRODUCT_TITLE),
                    self::IS_SHORT_CONTENT          => $post->getShortContent() ? true : false,
                    self::POSITION_HOMEPAGE         => $post->getData(self::POST_POSITION),
                    Data::MOBILE_MAIN_CONTENT       => $post->getData(Data::MOBILE_MAIN_CONTENT),
                    Data::MOBILE_SUB_CONTENT        => $post->getData(Data::MOBILE_SUB_CONTENT),
                    Data::IS_SUB_CONTENT            => $post->getData(Data::MOBILE_SUB_CONTENT) ? true : false,
                ];

                if ($post->getFeaturedImage() && file_exists($this->config->getMediaPath($post->getFeaturedImage()))) {
                    $result[$post->getId()][PostInterface::FEATURED_IMAGE] = [
                        [
                            'name' => $post->getFeaturedImage(),
                            'url'  => $this->config->getMediaUrl($post->getFeaturedImage()),
                            'size' => filesize($this->config->getMediaPath($post->getFeaturedImage())),
                            'type' => 'image',
                        ],
                    ];
                }

                if ($post->getData(Data::POST_DATA_HOME_IMAGE) &&
                    file_exists($this->config->getMediaPath($post->getData(Data::POST_DATA_HOME_IMAGE)))) {
                    $result[$post->getId()][Data::POST_DATA_HOME_IMAGE] = [
                        [
                            'name' => $post->getData(Data::POST_DATA_HOME_IMAGE),
                            'url'  => $this->config->getMediaUrl($post->getData(Data::POST_DATA_HOME_IMAGE)),
                            'size' => filesize($this->config->getMediaPath($post->getData(Data::POST_DATA_HOME_IMAGE))),
                            'type' => 'image',
                        ],
                    ];
                }

                $result[$post->getId()]['links']['products'] = [];
                $cnt = 1;

                foreach ($post->getRelatedProducts() as $product) {
                    if ($product && $product->getId()) {
                        $result[$post->getId()]['links']['products'][] = [
                            'id'        => $product->getId(),
                            'name'      => $product->getName(),
                            'status'    => $this->status->getOptionText($product->getStatus()),
                            'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
                            'position'  => $cnt++,
                            'value'     => $product->getData(Data::RELATED_PRODUCT_VALUE),
                        ];
                    }
                }
            }
        }

        return $result;
    }
}

<?php

namespace Mirasvit\Blog\Ui\Post\Form;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Blog\Api\Data\PostInterface;
use Mirasvit\Blog\Api\Repository\PostRepositoryInterface;
use Mirasvit\Blog\Model\Config;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var Status
     */
    protected $status;

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

        foreach ($this->collection as $post) {
            $post = $this->postRepository->get($post->getId());

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

                'is_short_content' => $post->getShortContent() ? true : false,
            ];

            if ($post->getFeaturedImage()) {
                $result[$post->getId()]['featured_image'] = [
                    [
                        'name' => $post->getFeaturedImage(),
                        'url'  => $this->config->getMediaUrl($post->getFeaturedImage()),
                        'size' => filesize($this->config->getMediaPath($post->getFeaturedImage())),
                        'type' => 'image',
                    ],
                ];
            }

            $result[$post->getId()]['links']['products'] = [];
            foreach ($post->getRelatedProducts() as $product) {
                $result[$post->getId()]['links']['products'][] = [
                    'id'        => $product->getId(),
                    'name'      => $product->getName(),
                    'status'    => $this->status->getOptionText($product->getStatus()),
                    'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
                ];
            }
        }

        return $result;
    }
}

<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Post\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Url\EncoderInterface;
use Mirasvit\Blog\Api\Data\CategoryInterface;
use SM\InspireMe\Helper\Data;

/**
 * Class RelatedProducts
 * @package SM\InspireMe\Block\Post\View
 */
class RelatedProducts extends \Mirasvit\Blog\Block\Post\View\RelatedProducts
{
    /**
     * @var \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $topicCollectionFactory;

    /**
     * @var \Magento\Framework\Url\EncoderInterface|null
     */
    protected $urlEncoder;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * RelatedProducts constructor.
     * @param Data $dataHelper
     * @param \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $topicCollectionFactory
     * @param Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     */
    public function __construct(
        \SM\InspireMe\Helper\Data $dataHelper,
        \Mirasvit\Blog\Model\ResourceModel\Category\CollectionFactory $topicCollectionFactory,
        Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder = null
    ) {
        parent::__construct($context);
        $this->topicCollectionFactory = $topicCollectionFactory;
        $this->urlEncoder = $urlEncoder ?: ObjectManager::getInstance()->get(EncoderInterface::class);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return array
     */
    public function getRelatedProducts()
    {
        return $this->getCurrentPost()->getRelatedProducts();
    }

    /**
     * Check if Related Products Block Type is Ingredient or not
     *
     * @return bool
     */
    public function isIngredientBlockType()
    {
        /** @var \Mirasvit\Blog\Model\Post $currentArticle */
        $currentArticle = $this->getCurrentPost();

        $parentIds = $currentArticle->getCategoryIds();
        $appliedTopicIds = explode(',', $this->dataHelper->getShopIngredientConfig());

        return (bool)count(array_intersect($parentIds, $appliedTopicIds));
    }

    /**
     * Get post parameters.
     *
     * @param Product $product
     * @return array
     */
    public function getAddToCartPostParams(Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlEncoder->encode($url),
            ]
        ];
    }
}

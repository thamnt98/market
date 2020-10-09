<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Helper;

use DateTime;
use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Blog\Model\Post;
use Zend_Json_Encoder;

/**
 * Class Data
 * @package SM\InspireMe\Helper
 */
class Data extends AbstractHelper
{
    const PAGING = 'blog/paging/paging_value';

    const HOMEPAGE_BLOCK_POSITION = 'blog/post_homepage_block/position_type';
    const CONFIG_POSITION_SELECTED = 'selected';
    const CONFIG_POSITION_MOST_VIEW = 'most_view';
    const CONFIG_POSITION_RECENT_UPLOAD = 'recent_upload';

    const MP_PRESET_CONFIG = 'blog/most_popular/preset_most_popular';
    const MP_POSITION = 'mp_position';
    const MP_BASED_ON = 'based_on';
    const MP_SELECT_ARTICLE = 'select_article';
    const MP_SELECT_ARTICLE_ID = 'select_article_id';

    const GTM_POST_DATA_NAME  = 'blogPost';
    const GTM_POST_EVENT_NAME = 'inspire_me_homepage';
    const GTM_POST_EVENT_CLICK_NAME = 'inspire_me_click';
    const GTM_POST_EVENT_CLICK_VIEW = 'inspire_me_click_view';
    const GTM_POST_EVENT_ARTICLE_ENGAGEMENT = 'article_engagement';
    const GTM_POST_EVENT_PRODUCT_CLICK = 'product_click';
    const GTM_POST_EVENT_PRODUCT_VIEW = 'product_view';
    const PRODUCT_EVENT_ADD_TO_CART = 'addToCart';

    const GTM_POST_ALL_VIEW_EVENT_NAME = 'see_all_inspire_me_homepage';

    const POST_DATA_HOME_IMAGE = 'home_image';

    const RELATED_PRODUCT_POSITION = 'product_position';
    const RELATED_PRODUCT_VALUE = 'product_value';
    const RELATED_PRODUCT_TITLE = 'related_products_title';
    const CONFIG_BLOCK_SHOP_INGREDIENT = 'blog/related_product_block/shop_ingredient';

    const MOBILE_MAIN_CONTENT = 'mobile_main_content';
    const MOBILE_SUB_CONTENT  = 'mobile_sub_content';
    const IS_SUB_CONTENT      = 'is_sub_content';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var AdapterFactory
     */
    protected $imageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * Data constructor.
     * @param Context $context
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param StoreManagerInterface $storeManager
     * @param Json $serializer
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        AdapterFactory $imageFactory,
        StoreManagerInterface $storeManager,
        Json $serializer
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
    }

    /**
     * Resize Image
     *
     * @param $image
     * @param null $width
     * @param null $height
     * @return bool|string
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function getImageResize($image, $width = null, $height = null)
    {
        $absolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath('blog/' . $image);
        if (!file_exists($absolutePath)) {
            return false;
        }

        $resizePath = 'blog/resized/' . $width . '/' . $height . '/';
        $destination = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath($resizePath) . $image;

        if (!file_exists($destination)) {
            $imageResize = $this->imageFactory->create();
            $imageResize->open($absolutePath);
            $imageResize->constrainOnly(true);
            $imageResize->keepTransparency(true);
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(true);
            $imageResize->resize($width, $height);

            $imageResize->save($destination);
        }

        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $resizePath . $image;
    }

    /**
     * @return mixed
     */
    public function getPagingConfig()
    {
        return $this->scopeConfig->getValue(self::PAGING);
    }

    /**
     * Prepare GTM data from Post
     * @param Post $post
     *
     * @return string
     */
    public function prepareGtmData($post)
    {
        try {
            $date = new DateTime($post->getData('published_date'));
            $publishedDate = $date->format('Y-m-d');
        } catch (Exception $e) {
            $publishedDate = null;
        }

        return Zend_Json_Encoder::encode([
            'articleId'       => $post->getUrlKey(),
            'articleTitle'    => trim($post->getName()),
            'articleCategory' => implode(',', $this->getAllCategoryName($post)),
            'articleSource'   => $post->getAuthor() ? $post->getAuthor()->getName() : '',
            'articlePresent'  => 'No',
            'publishedDate'   => $publishedDate
        ], true);
    }

    /**
     * Prepare GTM data from Post
     * @param Post $post
     *
     * @return string
     */
    public function prepareGtmDataInspireMe($post)
    {
        try {
            $date = new DateTime($post->getData('published_date'));
            $publishedDate = $date->format('Y-m-d');
        } catch (Exception $e) {
            $publishedDate = null;
        }
        $tagGTM = 'Not available';
        $tags = $post->getTags();
        if (count($tags)) {
            $tagGTM = "";
            foreach ($tags as $tag) {
                $tagGTM.= $tag->getName() . ', ';
            }
            $tagGTM = trim($tagGTM, ', ');
        }

        return Zend_Json_Encoder::encode([
            'content_title'         => trim($post->getName()),
            //Todo Change Value
            'content_source'        => "Transmart",
            'content_category'      => implode(',', $this->getAllCategoryName($post)),
            'content_creator'       => $post->getAuthor() ? $post->getAuthor()->getName() : '',
            'content_publisheddate' => $publishedDate,
            'article_id'            => $post->getUrlKey(),
            'article_tags'          => $tagGTM

        ], true);
    }

    /**
     * @param \Mirasvit\Blog\Model\Post $post
     *
     * @return array
     */
    public function getAllCategoryName($post)
    {
        return $post->getCategories()->getColumnValues('name');
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return array
     */
    public function getMostPopularConfig($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        $config = $this->scopeConfig->getValue(
            self::MP_PRESET_CONFIG,
            $scopeType,
            $scopeCode
        );

        return $this->serializer->unserialize($config);
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return int
     */
    public function getHomepagePositionConfig($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::HOMEPAGE_BLOCK_POSITION,
            $scopeType,
            $scopeCode
        );
    }

    /**
     * @param string $scopeType
     * @param null $scopeCode
     * @return string|null
     */
    public function getShopIngredientConfig($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_BLOCK_SHOP_INGREDIENT,
            $scopeType,
            $scopeCode
        );
    }
}

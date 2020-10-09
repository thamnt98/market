<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Api\Data;

/**
 * Interface PostDetailInterface
 * @package SM\InspireMe\Api\Data
 */
interface PostDetailInterface
{
    const ID = 'entity_id';

    const NAME             = 'name';
    const TYPE             = 'type';
    const STATUS           = 'status';
    const SHORT_CONTENT    = 'short_content';
    const URL_KEY          = 'url_key';
    const CREATED_AT       = 'created_at';
    const TAGS             = 'tags';
    const FORMAT_CREATED_AT = 'format_created_at';
    const TOPIC             = 'topic';
    const TOPIC_NAME        = 'topic_name';
    const ARTICLE_AUTHOR    = 'article_author';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getShortContent();

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getFormatCreatedAt();

    /**
     * @return string
     */
    public function getFeaturedImageUrl();

    /**
     * @return \Mirasvit\Blog\Api\Data\TagInterface[]
     */
    public function getTags();

    /**
     * @return int
     */
    public function getIsShopIngredient();

    /**
     * @return int
     */
    public function getShowHotSpot();

    /**
     * @return \SM\InspireMe\Api\Data\LookbookDetailInterface
     */
    public function getHotSpot();

    /**
     * @return string
     */
    public function getMobileMainContent();

    /**
     * @return string
     */
    public function getMobileSubContent();

    /**
     * @return string
     */
    public function getTopicName();

    /**
     * @param string $data
     * @return $this
     */
    public function setTopicName($data);

    /**
     * @return string
     */
    public function getArticleAuthor();

    /**
     * @param string $data
     * @return $this
     */
    public function setArticleAuthor($data);
}

<?php


namespace SM\InspireMe\Api\Data;

/**
 * Interface PostListingInterface
 * @package SM\InspireMe\Api\Data
 */
interface PostListingInterface
{
    const ID                = 'entity_id';
    const NAME              = 'name';
    const SHORT_CONTENT     = 'short_content';
    const PUBLISHED_DATE    = 'published_date';
    const HOME_IMAGE        = 'home_image';
    const TAG_NAME          = 'tag_name';
    const TOPIC_NAME        = 'topic_name';
    const CREATED_AT        = 'created_at';
    const FEATURE_IMAGE_URL = 'featured_image_url';
    const POSITION          = 'position';
    const FORMAT_CREATED_AT = 'format_created_at';
    const GTM_CREATED_AT    = 'gtm_created_at';
    const SOURCE            = 'author';
    const ARTICLE_LIST      = 'article_list';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getShortContent();

    /**
     * @param string $shortContent
     * @return $this
     */
    public function setShortContent($shortContent);

    /**
     * @return string
     */
    public function getPublishedDate();

    /**
     * @param string $date
     * @return $this
     */
    public function setPublishedDate($date);

    /**
     * @return string
     */
    public function getHomeImage();

    /**
     * @param string $url
     * @return $this
     */
    public function setHomeImage($url);

    /**
     * @return string
     */
    public function getTagName();

    /**
     * @param string $tagName
     * @return $this
     */
    public function setTagName($tagName);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getFeaturedImageUrl();

    /**
     * @param string $value
     * @return $this
     */
    public function setFeaturedImageUrl($value);

    /**
     * @return string
     */
    public function getHomeImageUrl();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param string $value
     * @return $this
     */
    public function setPosition($value);

    /**
     * @return string
     */
    public function getFormatCreatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setFormatCreatedAt($value);

    /**
     * @return string
     */
    public function getTopicName();

    /**
     * @param string $value
     * @return $this
     */
    public function setTopicName($value);

    /**
     * @return string
     */
    public function getGtmCreatedAt();

    /**
     * @param string $value
     * @return $this
     */
    public function setGtmCreatedAt($value);

    /**
     * @return string
     */
    public function getSource();

    /**
     * @param string $value
     * @return $this
     */
    public function setSource($value);

    /**
     * @return string
     */
    public function getArticleList();

    /**
     * @param string $value
     * @return $this
     */
    public function setArticleList($value);
}


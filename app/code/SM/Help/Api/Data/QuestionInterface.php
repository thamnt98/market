<?php

/**
 * @category SM
 * @package SM_Help
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Chinhvd <chinhvd@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Help\Api\Data;

interface QuestionInterface
{
    const ID          = 'question_id';

    const TITLE       = 'title';
    const STATUS      = 'status';
    const URL_KEY     = 'url_key';
    const TOPIC_IDS   = 'topic_ids';
    const STORE_IDS   = 'store_ids';
    const CREATED_AT  = 'created_at';
    const CONTENT     = 'content';
    const SORT_ORDER  = 'sort_order';

    const TOPIC_NAME  = 'topic_name';
    const CONTENT_URL = 'content_url';
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return string
     */
    public function setTitle($title);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     * @return int
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $urlKey
     * @return string
     */
    public function setUrlKey($urlKey);

    /**
     * @return int
     */
    public function getTopicId();

    /**
     * @param int $value
     * @return int
     */
    public function setTopicId($value);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $value
     * @return string
     */
    public function setContent($value);

    /**
     * @param string $data
     * @return $this
     */

    public function setCreatedAt($data);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getStoreIds();

    /**
     * @param string $value
     * @return string
     */
    public function setStoreIds($value);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return string
     */
    public function getContentUrl();

    /**
     * @param string $data
     * @return $this
     */
    public function setContentUrl($data);

    /**
     * @return string
     */
    public function getTopicName();

    /**
     * @param string $data
     * @return $this
     */
    public function setTopicName($data);
}

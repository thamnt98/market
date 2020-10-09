<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 07 2020
 * Time: 6:36 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Api\Data;

interface CustomerMessageInterface
{
    const CUSTOMER_ID_KEY       = 'customer_id';
    const MESSAGE_KEY           = 'message';
    const IS_READ_KEY           = 'is_read';
    const CONTENT_KEY           = 'content';
    const MESSAGE_ID_KEY        = 'message_id';
    const IMAGE_KEY             = 'image';
    const EVENT_TYPE_KEY        = 'event';
    const EVENT_LABEL_KEY       = 'event_label';
    const TITLE_TYPE_KEY        = 'title';
    const START_DATE_KEY        = 'start_date';
    const END_DATE_KEY          = 'end_date';
    const PARAMS_KEY            = 'params';
    const REDIRECT_TYPE_KEY     = 'redirect_type';
    const REDIRECT_ID_KEY       = 'redirect_id';
    const REDIRECT_URL_KEY      = 'redirect_url';
    const CREATED_AT_KEY        = 'created_at';
    const TITLE_HIGHLIGHT_KEY   = 'highlight_title';
    const CONTENT_HIGHLIGHT_KEY = 'highlight_content';

    /**
     * @return string
     */
    public function getEvent();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setEvent($type);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setImage($url);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content);

    /**
     * @return int
     */
    public function getMessageId();

    /**
     * @param $id
     *
     * @return $this
     */
    public function setMessageId($id);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getStartDate();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setStartDate($date);

    /**
     * @return string
     */
    public function getEndDate();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setEndDate($date);

    /**
     * @return string
     */
    public function getRedirectType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setRedirectType($type);

    /**
     * @return string
     */
    public function getRedirectId();

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setRedirectId($id);

    /**
     * @return string
     */
    public function getRedirectUrl();

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setRedirectUrl($url);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return bool
     */
    public function getIsRead();

    /**
     * @param bool $isRead
     *
     * @return $this
     */
    public function setIsRead($isRead);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date);

    /**
     * @return string
     */
    public function getHighlightTitle();

    /**
     * @param string $str
     *
     * @return $this
     */
    public function setHighlightTitle($str);

    /**
     * @return string
     */
    public function getHighlightContent();

    /**
     * @param string $str
     *
     * @return $this
     */
    public function setHighlightContent($str);

    /**
     * @return string
     */
    public function getEventLabel();

    /**
     * @param string $str
     *
     * @return $this
     */
    public function setEventLabel($str);
}

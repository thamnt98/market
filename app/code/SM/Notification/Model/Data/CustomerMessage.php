<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 07 2020
 * Time: 6:51 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Data;

class CustomerMessage extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \SM\Notification\Api\Data\CustomerMessageInterface
{
    /**
     * @return string
     */
    public function getEvent()
    {
        return $this->_get(self::EVENT_TYPE_KEY);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setEvent($type)
    {
        $this->setData(self::EVENT_TYPE_KEY, $type);

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->_get(self::IMAGE_KEY);
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setImage($url)
    {
        $this->setData(self::IMAGE_KEY, $url);

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->_get(self::CONTENT_KEY);
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->setData(self::CONTENT_KEY, $content);

        return $this;
    }

    /**
     * @return int
     */
    public function getMessageId()
    {
        return $this->_get(self::MESSAGE_ID_KEY);
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setMessageId($id)
    {
        $this->setData(self::MESSAGE_ID_KEY, $id);

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailTemplate()
    {
        return $this->_get(self::EMAIL_TEMPLATE_KEY);
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    public function setEmailTemplate($template)
    {
        $this->setData(self::EMAIL_TEMPLATE_KEY, $template);

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE_TYPE_KEY);
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData(self::TITLE_TYPE_KEY, $title);

        return $this;
    }

    /**
     * @return string
     */
    public function getStartDate()
    {
        return $this->_get(self::START_DATE_KEY);
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setStartDate($date)
    {
        $this->setData(self::START_DATE_KEY, $date);

        return $this;
    }

    /**
     * @return string
     */
    public function getEndDate()
    {
        return $this->_get(self::END_DATE_KEY);
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setEndDate($date)
    {
        $this->setData(self::END_DATE_KEY, $date);

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectType()
    {
        return $this->_get(self::REDIRECT_TYPE_KEY);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setRedirectType($type)
    {
        $this->setData(self::REDIRECT_TYPE_KEY, $type);

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectId()
    {
        return $this->_get(self::REDIRECT_ID_KEY);
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setRedirectId($id)
    {
        $this->setData(self::REDIRECT_ID_KEY, $id);

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_get(self::REDIRECT_URL_KEY);
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setRedirectUrl($url)
    {
        $this->setData(self::REDIRECT_URL_KEY, $url);

        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID_KEY);
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        $this->setData(self::CUSTOMER_ID_KEY, $customerId);

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsRead()
    {
        return $this->_get(self::IS_READ_KEY);
    }

    /**
     * @param bool $isRead
     *
     * @return $this
     */
    public function setIsRead($isRead)
    {
        $this->setData(self::IS_READ_KEY, $isRead);

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT_KEY);
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setCreatedAt($date)
    {
        $this->setData(self::CREATED_AT_KEY, $date);

        return $this;
    }

    /**
     * @return string
     */
    public function getHighlightContent()
    {
        return $this->_get(self::CONTENT_HIGHLIGHT_KEY);
    }

    /**
     * @param string $date
     *
     * @return $this
     */
    public function setHighlightContent($date)
    {
        $this->setData(self::CONTENT_HIGHLIGHT_KEY, $date);

        return $this;
    }

    /**
     * @return string
     */
    public function getHighlightTitle()
    {
        return $this->_get(self::TITLE_HIGHLIGHT_KEY);
    }

    /**
     * @param string $str
     *
     * @return $this
     */
    public function setHighlightTitle($str)
    {
        $this->setData(self::TITLE_HIGHLIGHT_KEY, $str);

        return $this;
    }

    /**
     * @return string
     */
    public function getEventLabel()
    {
        return $this->_get(self::EVENT_LABEL_KEY);
    }

    /**
     * @param string $str
     *
     * @return $this
     */
    public function setEventLabel($str)
    {
        $this->setData(self::EVENT_LABEL_KEY, $str);

        return $this;
    }
}

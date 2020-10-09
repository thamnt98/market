<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 15 2020
 * Time: 9:54 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Api\Data\Queue;

interface GeneralInterface
{
    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $event
     *
     * @return $this
     */
    public function setEvent($event);

    /**
     * @return string
     */
    public function getEvent();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setCustomerId($id);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setMessageId($id);

    /**
     * @return int
     */
    public function getMessageId();
}

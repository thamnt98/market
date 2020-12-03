<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: June, 08 2020
 * Time: 1:57 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Api\Data;

interface CustomerDeviceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID          = 'id';
    const CUSTOMER_ID = 'customer_id';
    const TOKEN       = 'token';
    const CREATED_AT  = 'created_at';
    const TYPE        = 'type';
    const DEVICE_ID   = 'device_id';
    const STATUS      = 'status';

    const STATUS_ENABLE  = 1;
    const STATUS_DISABLE = 0;

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

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
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getDeviceId();

    /**
     * @param string $deviceId
     *
     * @return $this
     */
    public function setDeviceId($deviceId);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);
}

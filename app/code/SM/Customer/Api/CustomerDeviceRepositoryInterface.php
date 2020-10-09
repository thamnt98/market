<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: June, 08 2020
 * Time: 1:56 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface CustomerDeviceRepositoryInterface
{
    /**
     * Save Device
     *
     * @param \SM\Customer\Api\Data\CustomerDeviceInterface $device
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceInterface
     * @throws CouldNotSaveException
     */
    public function save(\SM\Customer\Api\Data\CustomerDeviceInterface $device);

    /**
     * Retrieve Device
     *
     * @param int $id
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceInterface
     * @throws NoSuchEntityException
     */
    public function get($id);

    /**
     * Retrieve Device
     *
     * @param string $deviceId
     * @param int    $customerId
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceInterface
     * @throws NoSuchEntityException
     */
    public function getByDeviceId($deviceId, $customerId);

    /**
     * Retrieve Devices matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \SM\Customer\Api\Data\CustomerDeviceSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Device
     *
     * @param \SM\Customer\Api\Data\CustomerDeviceInterface $device
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\SM\Customer\Api\Data\CustomerDeviceInterface $device);

    /**
     * Delete Device by ID
     *
     * @param string $id
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id);

    /**
     * @param string $deviceId
     * @param int    $customerId
     *
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteByDeviceId($deviceId, $customerId);
}

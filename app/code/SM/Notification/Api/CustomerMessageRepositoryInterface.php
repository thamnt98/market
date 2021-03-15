<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 08 2020
 * Time: 1:41 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Api;

interface CustomerMessageRepositoryInterface
{
    /**
     * Get Customer Notification Messages
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \SM\Notification\Api\CustomerMessageResultInterface
     */
    public function getMobileList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get Customer Notification Messages
     *
     * @param int                                            $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int                                            $isMobile
     *
     * @return \SM\Notification\Api\CustomerMessageResultInterface
     */
    public function getList($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $isMobile = 0);

    /**
     * @param int    $customerId
     * @param int[]  $messageIds
     * @param string $type
     *
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function updateReadByIds($customerId, $messageIds, $type);

    /**
     * @param int                                                 $customerId
     * @param string                                              $type
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return int
     */
    public function updateReadAll(
        $customerId,
        $type,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    );

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function getCountUnread(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @return \SM\Notification\Api\Data\NotificationTypeInterface[]
     */
    public function getEnabledEvents();
}

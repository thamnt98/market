<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: December, 02 2020
 * Time: 3:47 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Plugin\Magento_Customer\CustomerData;

class Customer
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Customer constructor.
     *
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     */
    public function __construct(
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
    ) {
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * @param \Magento\Customer\CustomerData\Customer $subject
     * @param array                                   $result
     *
     * @return array
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        if ($this->currentCustomer->getCustomerId()) {
            $result['cus_id'] = $this->currentCustomer->getCustomerId();
        }

        return $result;
    }

}

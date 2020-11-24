<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: June, 08 2020
 * Time: 1:53 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Model;

class CustomerDevice extends \Magento\Framework\Model\AbstractModel
{
    const NEW_DEVICE_KEY = 'is_new_device';

    const DESKTOP_TYPE = 'desktop';

    protected $_eventPrefix = 'customer_device';
    protected $_eventObject = 'device';

    /**
     * Initialize resource model
     */
    public function _construct()
    {
        $this->_init(\SM\Customer\Model\ResourceModel\CustomerDevice::class);
    }
}

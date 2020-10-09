<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace Trans\Customer\Controller\Address;

/**
 * Class NewAction
 * @package Trans\Customer\Controller\Address
 */
class NewAction extends \Magento\Customer\Controller\Address\NewAction
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward
     */
    public function execute()
    {
        if ($this->_getSession()->isLoggedIn()) {
            $limit = $this->_objectManager->get(\Trans\Customer\Helper\Config::class)
                ->getConfigValue('sm_customer/customer_address_limit/limit');
            $addressList = $this->_getSession()->getCustomer()->getAddressesCollection();
            if ($addressList->getSize() >= (int)$limit) {
                $this->messageManager->addNoticeMessage(
                    __(
                        'You can only save up to %1 addresses. To add a new one, please delete the existing address.',
                        $limit
                    )
                );
                return $this->_redirect('customer/address');
            }
        }
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('form');
    }
}

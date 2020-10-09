<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Customer\Plugin\Magento\Customer\Controller\Account;


use Magento\Customer\Controller\Account\Logout as BaseLogout;
use Magento\Framework\Controller\ResultFactory;

class Logout
{
    /**
     * @var ResultFactory
     */
    private $result;

    /**
     * Logout Plugin constructor.
     * @param ResultFactory $result
     */
    public function __construct(ResultFactory $result)
    {
        $this->result = $result;
    }

    /**
     * @param BaseLogout $subject
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function afterExecute(BaseLogout $subject)
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->result->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('');
        return $resultRedirect;
    }
}

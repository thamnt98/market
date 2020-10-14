<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2019 PT CTCORP Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Controller\Address;

/**
 * Class Index
 * @package Trans\Customer\Controller\Address
 */
class Index extends \Magento\Customer\Controller\Address\Index
{
    /**
     * Customer addresses list
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}

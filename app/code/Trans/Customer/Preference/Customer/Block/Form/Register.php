<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Customer\Preference\Customer\Block\Form;

use Magento\Customer\Block\Form\Register as MageRegister;

/**
 * Class Register
 */
class Register extends MageRegister
{
	/**
	 * set form data register
	 */
	protected function _construct() {
	    $data = new \Magento\Framework\DataObject();
	    $data->setFullnameLabel('Fullname');
		if($this->_customerSession->getSocialRegister()) {
			$socialData = $this->_customerSession->getSocialRegister();
	        $data->setEmail($socialData['email']);
	        $data->setFullname($socialData['fullname']);
	        
	        $this->setData('form_data', $data);
		}

		$this->_customerSession->unsSocialRegister();
    }
}

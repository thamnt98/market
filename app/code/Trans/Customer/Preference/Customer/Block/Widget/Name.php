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

namespace Trans\Customer\Preference\Customer\Block\Widget;

use Magento\Customer\Block\Widget\Name as CustomerNameField;

/**
 * Class Name
 */
class Name extends CustomerNameField
{	
	/**
     * @inheritdoc
     */
    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('Trans_Customer::widget/name.phtml');
    }
}
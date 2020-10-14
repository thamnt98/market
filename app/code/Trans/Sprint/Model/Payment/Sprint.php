<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Sprint\Model\Payment;

/**
 * Class Sprint
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Sprint extends \Magento\Payment\Model\Method\AbstractMethod {
	/**
	 * {@inheritdoc}
	 */
	protected $_code = 'sprint';

	/**
	 * {@inheritdoc}
	 */
	protected $_isInitializeNeeded = true;
}

<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Observer;

use Magento\Framework\Event\ObserverInterface;

class DataAssignObserver implements ObserverInterface {

	/**
	 * @param \Magento\Framework\Event\Observer $observer
	 * @return this
	 */
	public function execute(\Magento\Framework\Event\Observer $observer) {
		$quote = $observer->getQuote();
		$order = $observer->getOrder();

		if ($quote->getKlikbcaUserid()) {
			$order->setKlikbcaUserid($quote->getKlikbcaUserid());
		}

		if ($quote->getSprintTermChannelid()) {
			$order->setSprintTermChannelid($quote->getSprintTermChannelid());
		}

		if ($quote->getServiceFee()) {
			$order->setServiceFee($quote->getServiceFee());
		}

		return $this;
	}
}

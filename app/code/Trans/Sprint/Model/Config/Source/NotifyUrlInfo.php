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
namespace Trans\Sprint\Model\Config\Source;

use Magento\Config\Model\Config\CommentInterface;
use Trans\Sprint\Helper\Config;

/**
 * Class NotifyUrlInfo
 */
class NotifyUrlInfo implements CommentInterface {
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	public $storeManager;

	/**
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		$this->storeManager = $storeManager;
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function getCommentText($elementValue) {
		return '<b>' . __('*Notify URL : %1', $this->getNotifyUrl()) . '</b>';
	}

	/**
	 * Get notify url
	 *
	 * @return string
	 */
	protected function getNotifyUrl() {
		return $this->storeManager->getStore()->getBaseUrl() . Config::NOTIFY_URL;
	}
}

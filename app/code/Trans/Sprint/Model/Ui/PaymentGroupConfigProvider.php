<?php
/**
 *
 * @author      Imam Kusuma<ikusuma@Trans.com>
 *
 * @package     Trans_Sprint
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 *
 * @copyright   Copyright © 2019 Trans. All rights reserved.
 * @link        http://www.Trans.com Driving Digital Commerce
 *
 */

namespace Trans\Sprint\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class PaymentGroupConfigProvider
 */
class PaymentGroupConfigProvider implements ConfigProviderInterface {
	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $jsonSerialize;

	/**
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	 */
	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Serialize\Serializer\Json $jsonSerialize
	) {
		$this->scopeConfig   = $scopeConfig;
		$this->jsonSerialize = $jsonSerialize;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfig() {
		$config = [
			'payment' => [
				'group' => [
					'label_array' => $this->getPaymentGroupsLabel(),
				],
			],
		];

		return $config;
	}

	/**
	 * Get Payment groups label
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	protected function getPaymentGroupsLabel() {
		$array             = [];
		$configGroupsLabel = $this->scopeConfig->getValue('payment/group/label', ScopeInterface::SCOPE_STORE, null);

		if (!empty($configGroupsLabel)) {
			$unserialize = $this->jsonSerialize->unserialize($configGroupsLabel);

			foreach ($unserialize as $key => $row) {
				if ($row['enable']) {
					$data['label'] = $row['group_label'];
					$data['code']  = $row['group_code'];

					$array[$row['group_code']] = $data;
				}
			}
		}

		return $array;
	}
}

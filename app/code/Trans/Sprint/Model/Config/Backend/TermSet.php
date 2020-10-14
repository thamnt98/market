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

namespace Trans\Sprint\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Trans\Sprint\Helper\Data as DataHelper;

/**
 * Class TermSet
 */
class TermSet extends ConfigValue {
	/**
	 * @var PaymentConfig
	 */
	protected $paymentConfig;

	/**
	 * @var DataHelper
	 */
	protected $dataHelper;

	/**
	 * ShippingMethods constructor
	 *
	 * @param SerializerInterface $serializer
	 * @param DataHelper $dataHelper
	 * @param Context $context
	 * @param Registry $registry
	 * @param ScopeConfigInterface $config
	 * @param TypeListInterface $cacheTypeList
	 * @param AbstractResource|null $resource
	 * @param AbstractDb|null $resourceCollection
	 * @param array $data
	 */
	public function __construct(
		DataHelper $dataHelper,
		Context $context,
		Registry $registry,
		ScopeConfigInterface $config,
		TypeListInterface $cacheTypeList,
		AbstractResource $resource = null,
		AbstractDb $resourceCollection = null,
		array $data = []
	) {
		$this->dataHelper = $dataHelper;

		parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
	}

	/**
	 * Prepare data before save
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	public function beforeSave() {
		/** @var array $value */
		$value = $this->getValue();
		unset($value['__empty']);
		$encodedValue = $this->dataHelper->serializeJson($value);

		$this->setValue($encodedValue);
	}

	/**
	 * Process data after load
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _afterLoad() {
		/** @var string $value */
		$value = $this->getValue();

		$decodedValue = !empty($value) ? $this->dataHelper->unserializeJson($value) : $value;

		$this->setValue($decodedValue);
	}
}

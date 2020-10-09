<?php
/**
 * @category Trans
 * @package  Trans_MasterPayment
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\MasterPayment\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime as LibDateTime;
use Trans\MasterPayment\Api\Data\MasterPaymentInterface;

/**
 * Class MasterPayment
 */
class MasterPayment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

	/**
	 * @var LibDateTime
	 */
	protected $date;

	/**
	 * Construct
	 *
	 * @param Context $context
	 * @param DateTime $date
	 */
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		LibDateTime $date
	) {
		$this->date = $date;

		parent::__construct($context);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _construct() {
		$this->_init(MasterPaymentInterface::TABLE_NAME, MasterPaymentInterface::ID);
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _beforeSave(AbstractModel $object) {
		if ($object->isObjectNew()) {
			if (!$object->hasCreatedAt()) {
				$object->setCreatedAt($this->date->gmtDate());
			}
		}

		$object->setUpdatedAt($this->date->gmtDate());

		return parent::_beforeSave($object);
	}
}

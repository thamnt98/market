<?php
/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Sprint\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Stdlib\DateTime\DateTime as LibDateTime;
use Trans\Sprint\Api\Data\BankInterface;

/**
 * Class SprintResponse
 */
class Bank extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

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
		$this->_init(BankInterface::TABLE_NAME, BankInterface::ID);
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

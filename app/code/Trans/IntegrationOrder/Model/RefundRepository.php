<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.com>
 *
 * Copyright © 2020 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Model;

use Magento\Framework\Exception\LocalizedException as Exception;
use Magento\Framework\Message\ManagerInterface;
use Trans\IntegrationOrder\Api\Data\RefundInterface;
use Trans\IntegrationOrder\Api\RefundRepositoryInterface;
use Trans\IntegrationOrder\Model\ResourceModel\Refund as RefundResourceModel;
use Trans\IntegrationOrder\Model\ResourceModel\Refund\CollectionFactory;

/**
 * Class RefundRepository
 */
class RefundRepository implements RefundRepositoryInterface {
	/**
	 * @var array
	 */
	private $instances = [];

	/**
	 * @var RefundInterface
	 */
	private $refundInterface;

	/**
	 * @var RefundResourceModel
	 */
	private $refundResourceModel;

	private $collectionFactory;

	/**
	 * @var ManagerInterface
	 */
	private $messageManager;

	/**
	 * RefundRepository constructor.
	 * @param RefundInterface $refundInterface
	 * @param RefundResourceModel $refundResourceModel
	 * @param CollectionFactory $collectionFactory
	 * @param ManagerInterface $messageManager
	 */
	public function __construct(
		RefundInterface $refundInterface,
		RefundResourceModel $refundResourceModel,
		CollectionFactory $collectionFactory,
		ManagerInterface $messageManager
	) {
		$this->refundInterface     = $refundInterface;
		$this->refundResourceModel = $refundResourceModel;
		$this->collectionFactory   = $collectionFactory;
		$this->messageManager      = $messageManager;
	}

	/**
	 * @param RefundInterface $refundInterface
	 * @return RefundInterface
	 * @throws \Exception
	 */
	public function save(RefundInterface $refundInterface) {
		try {
			$this->refundResourceModel->save($refundInterface);
		} catch (Exception $e) {
			$this->messageManager
				->addExceptionMessage(
					$e,
					'There was a error while saving the order ' . $e->getMessage()
				);
		}

		return $refundInterface;
	}
}
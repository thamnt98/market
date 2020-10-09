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
namespace Trans\MasterPayment\Controller\Test;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Trans\MasterPayment\Api\PaymentInterface;
use Trans\MasterPayment\Helper\Data;

/**
 * Class Authorization
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MatrixAdjustment extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {
	/**
	 * @var JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var PaymentInterface
	 */
	protected $paymentInterface;

	/**
	 * @var EventManager
	 */
	private $eventManager;

	/**
	 * @param Context          $context
	 * @param JsonFactory      $resultJsonFactory
	 * @param PaymentInterface $paymentInterface
	 * @param \Magento\Framework\Event\ManagerInterface as EventManager
	 */
	public function __construct(
		Context $context,
		JsonFactory $resultJsonFactory,
		PaymentInterface $paymentInterface,
		EventManager $eventManager,
		Data $dataHelper
	) {
		parent::__construct($context);

		$this->resultJsonFactory = $resultJsonFactory;
		$this->paymentInterface  = $paymentInterface;
		$this->eventManager      = $eventManager;
		$this->dataHelper        = $dataHelper;

		$this->logger = $dataHelper->getLogger();
	}

	/**
	 * @return sting
	 */
	public function execute() {
		$postData    = $this->getRequest()->getParams();
		$incrementId = '';
		if ($postData) {
			$incrementId = $postData['increment_id'];
		}

		$this->eventManager->dispatch('trans_master_payment_matrix_adjustment', ['order_id' => $incrementId]);
	}

	/**
	 * [createCsrfValidationException description]
	 * @param  RequestInterface $request
	 * @return mixing
	 */
	public function createCsrfValidationException(RequestInterface $request):  ? InvalidRequestException {
		return null;
	}

	/**
	 * [validateForCsrf description]
	 * @param  RequestInterface $request
	 * @return boolean
	 */
	public function validateForCsrf(RequestInterface $request) :  ? bool {
		return true;
	}
}
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
use Trans\MasterPayment\Api\PaymentInterface;

/**
 * Class Authorization
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentId extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {
	/**
	 * @var JsonFactory
	 */
	protected $resultJsonFactory;

	/**
	 * @var PaymentInterface
	 */
	protected $paymentInterface;

	/**
	 * @param Context          $context
	 * @param JsonFactory      $resultJsonFactory
	 * @param PaymentInterface $paymentInterface
	 */
	public function __construct(
		Context $context,
		JsonFactory $resultJsonFactory,
		PaymentInterface $paymentInterface
	) {
		parent::__construct($context);

		$this->resultJsonFactory = $resultJsonFactory;
		$this->paymentInterface  = $paymentInterface;
	}

	/**
	 * @return sting
	 */
	public function execute() {
		$postData = $this->getRequest()->getParams();

		$data = ["error" => "Parameter Increment Id is Null"];
		if ($postData) {
			$incrementId = $postData['increment_id'];
			$data        = $this->paymentInterface->getMasterPaymentData($incrementId);
		}
		$result = $this->resultJsonFactory->create();
		return $result->setData($data);
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
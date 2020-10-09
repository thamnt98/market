<?php
/**
 * @category Trans
 * @package  Trans_DigitalProduct
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Rifai <muhammad.rifai@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\DigitalProduct\Model;

use Trans\DigitalProduct\Api\DigitalProductGetOperatorInterface;
use Trans\DigitalProduct\Helper\Config;

/**
 * DigitalProductGetOperator
 */
class DigitalProductGetOperator implements DigitalProductGetOperatorInterface {

	/**
	 * @var \Trans\DigitalProduct\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Trans\DigitalProduct\Helper\Data $dataHelper
	 */
	function __construct(
		\Trans\DigitalProduct\Helper\Data $dataHelper
	) {
		$this->dataHelper = $dataHelper;

		$this->config = $this->dataHelper->getConfigHelper();
		$this->logger = $this->dataHelper->getLogger();
	}

	/**
	 * Operator PDAM
	 *
	 * @param  int $productId
	 * @return array
	 */
	public function pdam($productId) {
		$action      = Config::ACTION_OPERATOR;
		$path        = Config::URL_PATH_PDAM;
		$productData = [
			"product_id" => $productId,
		];

		$data = $this->dataHelper->doHitApiAlteraPost($productData, $action, $path);

		return $data;
	}
}
<?php
/**
 *
 * @author      Imam Kusuma<imamkusuma92@gmail.com>
 *
 * @category    Trans
 * @package     Trans_Sprint
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 *
 * @copyright   Copyright © 2019 Trans. All rights reserved.
 * @link        http://www.Trans.com Driving Digital Commerce
 *
 */
namespace Trans\Sprint\Block\Adminhtml\Form\Field\Select;

/**
 * PaymentGroup
 */
class PaymentGroup extends \Magento\Backend\Block\Template {
	/**
	 * @var \Magento\Payment\Model\Config
	 */
	protected $paymentConfig;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Payment\Model\Config $paymentConfig,
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Payment\Model\Config $paymentConfig,
		array $data = []
	) {
		$this->paymentConfig = $paymentConfig;
		parent::__construct($context, $data);
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	protected function _toHtml() {
		$inputId   = $this->getInputId();
		$inputName = $this->getInputName();
		$colName   = $this->getColumnName();
		$column    = $this->getColumn();

		$string = '<select id="' . $inputId . '"' .
			' name="' . $inputName . '" <%- ' . $colName . ' %> ' .
			($column['size'] ? 'size="' . $column['size'] . '"' : '') .
			' class="' . (isset($column['class']) ? $column['class'] : 'input-text') . '">';
		$string .= '<option value=""></option>';
		if ($this->getPaymentGroup()) {
			foreach ($this->getPaymentGroup() as $row) {
				$string .= '<option value="' . $row['code'] . '">' . $row['label'] . '</option>';
			}
		}
		$string .= '</select>';

		return $string;
	}

	/**
	 * Get payment groups
	 *
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	protected function getPaymentGroup() {
		$results = [];
		$groups  = $this->paymentConfig->getGroups();
		foreach ($groups as $code => $title) {
			$result          = [];
			$result['code']  = $code;
			$result['label'] = $title;
			$results[]       = $result;
		}

		return $results;
	}
}

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
namespace Trans\Sprint\Block\Adminhtml\Form\Field\Select;

/**
 * Term
 */
class Term extends \Magento\Backend\Block\Template {
	/**
	 * installment term set
	 */
	const INSTALLMENT_TERM_SET = 'installment_set/installment_term_set/term_set';

	/**
	 * @var \Trans\Sprint\Helper\Config
	 */
	protected $config;

	/**
	 * @var \Trans\Sprint\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Trans\Sprint\Helper\Data $dataHelper
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Trans\Sprint\Helper\Data $dataHelper,
		array $data = []
	) {
		$this->dataHelper = $dataHelper;
		$this->config     = $dataHelper->getConfigHelper();

		parent::__construct($context, $data);
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD)
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
		if ($this->getInstallmentTermSet()) {
			foreach ($this->getInstallmentTermSet() as $row) {
				$string .= '<option value="' . $row['value'] . '">' . $row['label'] . '</option>';
			}
		}
		$string .= '</select>';

		return $string;
	}

	/**
	 * Get installment term array
	 * @return array
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public function getInstallmentTermSet() {
		$terms   = null;
		$termSet = $this->config->getConfigValue(self::INSTALLMENT_TERM_SET);

		if (!empty($termSet)) {
			$configTerms = $this->dataHelper->unserializeJson($termSet);

			foreach ($configTerms as $key => $row) {
				$terms[] = array('value' => $row['term'], 'label' => $row['term']);
			}
		}

		return $terms;
	}
}

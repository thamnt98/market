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
 * Enable
 */
class Enable extends \Magento\Backend\Block\Template {
	/**
	 * @var \Trans\Sprint\Helper\Config
	 */
	protected $config;

	/**
	 * @var \Trans\Sprint\Helper\Data
	 */
	protected $dataHelper;

	/**
	 * @var \Magento\Config\Model\Config\Source\Yesno
	 */
	protected $configYesno;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Trans\Sprint\Helper\Data $dataHelper
	 * @param \Magento\Config\Model\Config\Source\Yesno $configYesno
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Trans\Sprint\Helper\Data $dataHelper,
		\Magento\Config\Model\Config\Source\Yesno $configYesno,
		array $data = []
	) {
		$this->configYesno = $configYesno;
		$this->dataHelper  = $dataHelper;
		$this->config      = $dataHelper->getConfigHelper();

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
		foreach ($this->configYesno->toOptionArray() as $row) {
			$string .= '<option value="' . $row['value'] . '">' . $row['label'] . '</option>';
		}
		$string .= '</select>';

		return $string;
	}
}

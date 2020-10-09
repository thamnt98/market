<?php
/**
 *
 * @author      Imam Kusuma<imamkusuma92@gmail.com>
 *
 * @category    Trans
 * @package     Trans_DigitalProduct
 * @license     https://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 *
 * @copyright   Copyright © 2019 Trans. All rights reserved.
 * @link        http://www.Trans.com Driving Digital Commerce
 *
 */

namespace Trans\DigitalProduct\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class ProductUrl
 */
class ProductUrl extends AbstractFieldArray {

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		array $data = []
	) {
		parent::__construct($context, $data);
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _prepareToRender() {
		$this->addColumn('product_id', ['label' => __('Product Id'), 'class' => 'required-entry']);
		$this->addColumn('product_url', ['label' => __('Product URL'), 'class' => 'required-entry']);
		$this->_addAfter = false;
	}
}

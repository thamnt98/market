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

namespace Trans\Sprint\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class PaymentGroup
 */
class PaymentGroup extends AbstractFieldArray {
	/**
	 * @var \Trans\Sprint\Block\Adminhtml\Form\Field\Select\EnableFactory
	 */
	protected $enableDisable;

	/**
	 * @var \Trans\Sprint\Block\Adminhtml\Form\Field\Select\PaymentGroupFactory
	 */
	protected $selectGroup;

	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Trans\Sprint\Block\Adminhtml\Form\Field\Select\PaymentGroupFactory $selectGroup
	 * @param \Trans\Sprint\Block\Adminhtml\Form\Field\Select\EnableFactory $enableDisable
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Trans\Sprint\Block\Adminhtml\Form\Field\Select\PaymentGroupFactory $selectGroup,
		\Trans\Sprint\Block\Adminhtml\Form\Field\Select\EnableFactory $enableDisable,
		array $data = []
	) {
		$this->selectGroup   = $selectGroup;
		$this->enableDisable = $enableDisable;

		parent::__construct($context, $data);
	}

	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD)
	 */
	protected function _prepareToRender() {
		$this->addColumn('group_code', ['label' => __('Code'), 'class' => 'required-entry', 'renderer' => $this->selectGroup->create()]);
		$this->addColumn('group_label', ['label' => __('Label'), 'class' => 'required-entry']);
		$this->addColumn('enable', ['label' => __('Enable'), 'class' => 'required-entry', 'renderer' => $this->enableDisable->create()]);
		$this->_addAfter = false;
	}
}

<?php

namespace SM\MyVoucher\Controller\Adminhtml\Promo\Quote;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Save extends \Magento\SalesRule\Controller\Adminhtml\Promo\Quote\Save{

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;


    public function __construct(\Magento\Backend\App\Action\Context $context,
                                \Magento\Framework\Registry $coreRegistry,
                                \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
                                \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
                                TimezoneInterface $timezone = null,
                                DataPersistorInterface $dataPersistor = null)
    {
        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter, $timezone, $dataPersistor);
        $this->timezone =  $timezone ?? \Magento\Framework\App\ObjectManager::getInstance()->get(
                TimezoneInterface::class
            );
        $this->dataPersistor = $dataPersistor ?? \Magento\Framework\App\ObjectManager::getInstance()->get(
                DataPersistorInterface::class
            );

    }

    public function execute()
    {
        $data = $this->getData();
        if ($data) {
            try {
                /** @var $model \Magento\SalesRule\Model\Rule */
                $model = $this->_objectManager->create(\Magento\SalesRule\Model\Rule::class);
                $this->_eventManager->dispatch(
                    'adminhtml_controller_salesrule_prepare_save',
                    ['request' => $this->getRequest()]
                );
                if (empty($data['from_date'])) {
                    $data['from_date'] = $this->timezone->formatDate();
                }

                $filterValues = ['from_date' => $this->_dateFilter];
                if ($this->getRequest()->getParam('to_date')) {
                    $filterValues['to_date'] = $this->_dateFilter;
                }
                $inputFilter = new \Zend_Filter_Input(
                    $filterValues,
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                if (!$this->checkRuleExists($model)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('The wrong rule is specified.'));
                }

                $session = $this->_objectManager->get(\Magento\Backend\Model\Session::class);

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addErrorMessage($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->dataPersistor->set('sale_rule', $data);
                    $this->_redirect('sales_rule/*/edit', ['id' => $model->getId()]);
                    return;
                }

                if (isset(
                        $data['simple_action']
                    ) && $data['simple_action'] == 'by_percent' && isset(
                        $data['discount_amount']
                    )
                ) {
                    $data['discount_amount'] = min(100, $data['discount_amount']);
                }
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);

                $data = $this->checkImage($data);

                $model->loadPost($data);

                $useAutoGeneration = (int)(
                    !empty($data['use_auto_generation']) && $data['use_auto_generation'] !== 'false'
                );
                $model->setUseAutoGeneration($useAutoGeneration);

                $session->setPageData($model->getData());

                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the rule.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('sales_rule/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('sales_rule/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('sales_rule/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('sales_rule/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the rule data. Please review the error log.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->_redirect('sales_rule/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('sales_rule/*/');
    }

    /**
     * Check if Cart Price Rule with provided id exists.
     *
     * @param \Magento\SalesRule\Model\Rule $model
     * @return bool
     */
    private function checkRuleExists(\Magento\SalesRule\Model\Rule $model): bool
    {
        $id = $this->getRequest()->getParam('rule_id');
        if ($id) {
            $model->load($id);
            if ($model->getId() != $id) {
                return false;
            }
        }
        return true;
    }

    public function checkImage(array $rawData)
    {
        $data = $rawData;
        if (isset($data['voucher_image'][0]['name'])) {
            $data['voucher_image'] = $data['voucher_image'][0]['name'];
        } else {
            $data['voucher_image'] = null;
        }
        return $data;
    }

    protected function getData()
    {
        $data = $this->getRequest()->getPostValue();
        $action = $data['simple_action'] ?? '';
        $skuSetType = [
            \SM\Promotion\Model\Data\Rule::TYPE_SETOF_FIXED_DISCOUNT,
            \Amasty\Rules\Helper\Data::TYPE_SETOF_PERCENT,
            \Amasty\Rules\Helper\Data::TYPE_SETOF_FIXED,
        ];
        $notShippingType = [
            \Amasty\Rules\Helper\Data::TYPE_GROUP_N,
            \Amasty\Rules\Helper\Data::TYPE_SETOF_FIXED,
            \Amasty\Rules\Helper\Data::TYPE_EACH_N_FIXED,
            \Amasty\Rules\Helper\Data::TYPE_EACH_M_AFT_N_FIX,
            \Amasty\Rules\Helper\Data::TYPE_XY_FIXED,
            \Amasty\Rules\Helper\Data::TYPE_XN_FIXED,
            \Amasty\Rules\Helper\Data::TYPE_AFTER_N_FIXED,
        ];
        $turnOffQtyType = [
            'ampromo_cart',
            \Amasty\Rules\Helper\Data::TYPE_GROUP_N
        ];
        $turnOffStepType = array_merge(
            ['ampromo_cart'],
            $skuSetType
        );

        if (!in_array($action, array_merge($skuSetType, \Amasty\Rules\Helper\Data::BUY_X_GET_Y))) {
            $data['extension_attributes']['amrules']['promo_skus'] = '';
            $data['extension_attributes']['amrules']['promo_cats'] = '';
        }

        if (in_array($action, $turnOffQtyType)) {
            $data['discount_qty'] = 0;
        }

        if (in_array($action, $notShippingType)) {
            $data['apply_to_shipping'] = 0;
        }

        if (in_array($action, $turnOffStepType)) {
            $data['discount_step'] = 1;
        }

        if (in_array($action, $skuSetType)) {
            $actionTrue = $data['rule']['actions'][1] ?? [];
            $data['rule']['actions'] = [];
            $data['rule']['actions'][1] = $actionTrue;
        }

        return $data;
    }
}
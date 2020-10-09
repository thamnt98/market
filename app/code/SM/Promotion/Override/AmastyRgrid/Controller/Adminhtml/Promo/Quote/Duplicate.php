<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Promotion
 *
 * Date: September, 29 2020
 * Time: 1:59 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Promotion\Override\AmastyRgrid\Controller\Adminhtml\Promo\Quote;

class Duplicate extends \Amasty\Rgrid\Controller\Adminhtml\Promo\Quote\Duplicate
{
    /**
     * @var \Magento\SalesRule\Api\RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * Duplicate constructor.
     *
     * @param \Magento\Backend\App\Action\Context            $context
     * @param \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\SalesRule\Api\RuleRepositoryInterface $ruleRepository
    ) {
        parent::__construct($context, $ruleRepository);
        $this->ruleRepository = $ruleRepository;
    }

    public function execute()
    {
        $ruleId = $this->getRequest()->getParam('id');

        if ($ruleId) {
            try {
                $rule = $this->ruleRepository->getById($ruleId);
                $rule->setRuleId(null);
                $ext = $rule->getExtensionAttributes();
                $ext->setCount(0);
                $rule->setExtensionAttributes($ext);
                $rule = $this->ruleRepository->save($rule);

                $this->messageManager->addSuccessMessage(__('The rule has been duplicated.'));

                return $this->_redirect('sales_rule/*/edit', ['id' => $rule->getRuleId()]);
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Exception $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('We can\'t duplicate the rule right now. Please review the log and try again.')
                );
            }

            return $this->_redirect('sales_rule/*/');
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a rule to duplicate.'));

        return $this->_redirect('sales_rule/*/');
    }
}

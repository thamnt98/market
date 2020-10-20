<?php
/**
 * SM\Review\Plugin\Model
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\Review\Plugin\Model;

/**
 * Class Review
 * @package SM\Review\Plugin\Model
 */
class Review
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * Review constructor.
     * @param \Magento\Framework\Module\Manager  $moduleManager
     */
    public function __construct(
        \Magento\Framework\Module\Manager  $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param \Magento\Review\Model\Review $subject
     * @param callable $proceed
     * @return array|bool
     */
    public function aroundValidate($subject, callable $proceed)
    {
        if ($this->moduleManager->isEnabled('SM_Review')) {
            return $this->modifiedValidate($subject);
        }
        return $proceed();
    }

    /**
     * @param \Magento\Review\Model\Review $subject
     * @return array|bool
     */
    private function modifiedValidate($subject)
    {
        $errors = [];

        if (!\Zend_Validate::is($subject->getDetail(), 'NotEmpty')) {
            $errors[] = __('Please enter a review.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}

<?php

namespace SM\CustomPrice\Plugin\Block;

use Magento\Framework\View\Element\AbstractBlock;

/*
 * Update key cache follow customer store
 */

class BlockPlugin
{

    protected $customerSession;

    /**
     * ProductCollection constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * @param AbstractBlock $subject
     * @param               $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetCacheKeyInfo(AbstractBlock $subject, $result)
    {
        if ($this->customerSession->isLoggedIn()) {
            $defaultStoreContext = 0;
            $omni_store_id       = $this->customerSession->getOmniStoreId();
            $storeContext        = $omni_store_id ?? $defaultStoreContext;
            $result[]            = $storeContext;
        }
        return $result;
    }
}

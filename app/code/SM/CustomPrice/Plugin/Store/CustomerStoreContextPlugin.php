<?php


namespace SM\CustomPrice\Plugin\Store;


class CustomerStoreContextPlugin
{
    const CONTEXT_REGION = 'CONTEXT_OMNI_STORE';

    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * \Magento\Framework\App\Http\Context::getVaryString is used by Magento to retrieve unique identifier for selected context,
     * so this is a best place to declare custom context variables
     */
    function beforeGetVaryString(\Magento\Framework\App\Http\Context $subject)
    {
        if ($this->customerSession->isLoggedIn()) {
            $defaultStoreContext = 0;
            $omni_store_id       = $this->customerSession->getOmniStoreId();
            $storeContext        = $omni_store_id ?? $defaultStoreContext;
            $subject->setValue(self::CONTEXT_REGION, $storeContext, $defaultStoreContext);

        }
    }
}

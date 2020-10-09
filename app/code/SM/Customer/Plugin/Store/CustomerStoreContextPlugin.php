<?php


namespace SM\Customer\Plugin\Store;


class CustomerStoreContextPlugin
{
    const CONTEXT_REGION = 'CONTEXT_REGION';
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
            $defaultRegionContext = 0;
            $region = $this->customerSession->getCustomerData()->getCustomAttribute('region');
            if (!empty($region)) {
                $regionId               = $region->getValue();
                $regionContext        = $regionId ?? $defaultRegionContext;
                $subject->setValue(self::CONTEXT_REGION, $regionContext, $defaultRegionContext);
            }

        }
    }
}

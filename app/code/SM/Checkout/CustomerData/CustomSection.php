<?php
namespace SM\Checkout\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session;

class CustomSection implements SectionSourceInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    public function __construct(Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    /**
     * @return array|bool[]|false[]
     */
    public function getSectionData()
    {
        return [
            'show' => ($this->customerSession->getFulfillment()) ? true : false
        ];
    }
}

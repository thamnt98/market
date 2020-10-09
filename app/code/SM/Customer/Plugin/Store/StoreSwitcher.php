<?php
/**
 * Class StoreSwitcher
 * @package SM\Customer\Plugin\Store
 * @author Son Nguyen <sonnn@smartosc.com>
 */

namespace SM\Customer\Plugin\Store;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreSwitcherInterface;

class StoreSwitcher
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * StoreSwitcher constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Update customer language after store view switching.
     *
     * @param StoreSwitcherInterface $subject
     * @param string $result
     * @param StoreInterface $fromStore store where we came from
     * @param StoreInterface $targetStore store where to go to
     * @param string $redirectUrl original url requested for redirect after switching
     * @return string url to be redirected after switching
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSwitch(
        StoreSwitcherInterface $subject,
        $result,
        StoreInterface $fromStore,
        StoreInterface $targetStore,
        string $redirectUrl
    ): string {
        try {
            if ($this->customerSession->getCustomerGroupId()) {
                $customer = $this->customerSession->getCustomer();
                $customer->setData('language_web', $targetStore->getCode());
                $this->customerRepository->save($customer->getDataModel());
            }
        } catch (\Exception $e) {
            return $result;
        }
        return $result;
    }
}

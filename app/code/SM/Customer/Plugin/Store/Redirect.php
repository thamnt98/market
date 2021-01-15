<?php
/**
 * Class StoreSwitcher
 * @package SM\Customer\Plugin\Store
 * @author Son Nguyen <sonnn@smartosc.com>
 */

namespace SM\Customer\Plugin\Store;

class Redirect
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * SwitchAction constructor.
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->coreSession = $coreSession;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Store\Controller\Store\Redirect $subject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeExecute(
        \Magento\Store\Controller\Store\Redirect $subject
    ) {
        $this->coreSession->start();
        if ($this->coreSession->getTransRedirect()) {
            $this->coreSession->unsTransRedirect();
        }
        $prefer = $subject->getRequest()->getParam('transmart-prefer');
        if ($prefer) {
            $prefer = str_replace("___", "/", $prefer);
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            if ($prefer == 'home' || $prefer == '') {
                $redirectUrl = $baseUrl;
            } else {
                $redirectUrl = $baseUrl . $prefer;
            }
            $this->coreSession->setTransRedirect($redirectUrl);
        }
    }
}

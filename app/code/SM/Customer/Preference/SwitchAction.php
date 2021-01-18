<?php

namespace SM\Customer\Preference;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreIsInactiveException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreSwitcherInterface;

class SwitchAction extends \Magento\Store\Controller\Store\SwitchAction
{
    /**
     * @var StoreSwitcherInterface
     */
    private $storeSwitcher;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $coreSession;

    /**
     * SwitchAction constructor.
     * @param ActionContext $context
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param HttpContext $httpContext
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param StoreSwitcherInterface|null $storeSwitcher
     */
    public function __construct(
        ActionContext $context,
        StoreCookieManagerInterface $storeCookieManager,
        HttpContext $httpContext,
        StoreRepositoryInterface $storeRepository,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        StoreSwitcherInterface $storeSwitcher = null
    ) {
        parent::__construct(
            $context,
            $storeCookieManager,
            $httpContext,
            $storeRepository,
            $storeManager
        );
        $this->messageManager = $context->getMessageManager();
        $this->coreSession = $coreSession;
        $this->storeSwitcher = $storeSwitcher ?: ObjectManager::getInstance()->get(StoreSwitcherInterface::class);
    }
    /**
     * Execute action
     *
     * @return void
     * @throws StoreSwitcher\CannotSwitchStoreException
     */
    public function execute()
    {
        $targetStoreCode = $this->_request->getParam(
            \Magento\Store\Model\StoreManagerInterface::PARAM_NAME
        );
        $fromStoreCode = $this->_request->getParam(
            '___from_store',
            $this->storeCookieManager->getStoreCodeFromCookie()
        );
        $requestedUrlToRedirect = $this->_redirect->getRedirectUrl();
        $redirectUrl = $requestedUrlToRedirect;

        $error = null;
        try {
            $fromStore = $this->storeRepository->get($fromStoreCode);
            $targetStore = $this->storeRepository->getActiveStoreByCode($targetStoreCode);
        } catch (StoreIsInactiveException $e) {
            $error = __('Requested store is inactive');
        } catch (NoSuchEntityException $e) {
            $error = __("The store that was requested wasn't found. Verify the store and try again.");
        }
        if ($error !== null) {
            $this->messageManager->addErrorMessage($error);
        } else {
            $redirectUrl = $this->storeSwitcher->switch($fromStore, $targetStore, $requestedUrlToRedirect);
        }
        $this->coreSession->start();
        if ($this->coreSession->getTransRedirect()) {
            $redirectUrl = $this->coreSession->getTransRedirect();
            $this->coreSession->unsTransRedirect();
        }
        $this->getResponse()->setRedirect($redirectUrl);
    }
}

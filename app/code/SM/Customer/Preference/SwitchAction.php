<?php

namespace SM\Customer\Preference;

use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Controller\Store\SwitchAction\CookieManager;
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
     * @var SessionManagerInterface
     */
    protected $coreSession;

    /**
     * SwitchAction constructor.
     * @param ActionContext $context
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param HttpContext $httpContext
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreManagerInterface $storeManager
     * @param StoreSwitcherInterface $storeSwitcher
     * @param CookieManager $cookieManager
     * @param SessionManagerInterface $coreSession
     */
    public function __construct(
        ActionContext $context,
        StoreCookieManagerInterface $storeCookieManager,
        HttpContext $httpContext,
        StoreRepositoryInterface $storeRepository,
        StoreManagerInterface $storeManager,
        StoreSwitcherInterface $storeSwitcher,
        CookieManager $cookieManager,
        SessionManagerInterface $coreSession
    ) {
        $this->messageManager = $context->getMessageManager();
        $this->coreSession = $coreSession;
        $this->storeSwitcher = $storeSwitcher ?: ObjectManager::getInstance()->get(StoreSwitcherInterface::class);
        parent::__construct(
            $context,
            $storeCookieManager,
            $httpContext,
            $storeRepository,
            $storeManager,
            $storeSwitcher,
            $cookieManager
        );
    }

    /**
     * Execute action
     *
     * @return void
     * @throws StoreSwitcher\CannotSwitchStoreException
     * @throws \Magento\Store\Model\StoreSwitcher\CannotSwitchStoreException
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

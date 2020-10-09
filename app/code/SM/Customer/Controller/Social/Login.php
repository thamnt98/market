<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Mageplaza
 * @package   Mageplaza_SocialLogin
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */
namespace SM\Customer\Controller\Social;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Mageplaza\SocialLogin\Controller\Social\Login as BaseLoginController;

/**
 * Class Login
 * @package SM\Customer\Controller\Social
 */
class Login extends BaseLoginController
{
    /**
     * @return ResponseInterface|Raw|ResultInterface|Login|void
     * @throws FailureToSendException
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute()
    {
        if ($this->checkCustomerLogin() && $this->session->isLoggedIn()) {
            $this->_redirect('customer/account');
            return;
        }
        $type = $this->apiHelper->setType($this->getRequest()->getParam('type'));
        $actionType = $this->getRequest()->getParam('action-type');
        $this->session->setSocialType($type);
        $this->session->setActionType($actionType);

        if (!$type) {
            $this->_forward('noroute');
            return;
        }

        try {
            $userProfile = $this->apiObject->getUserProfile($type);
        } catch (\Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/social.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            return $this->_appendJs("<script>window.close();</script>");
        }

        $this->session->setUserProfile($userProfile);
        try {
            $customer = $this->apiObject->getCustomerBySocial($userProfile->identifier, $type);
            if (!$customer->getId()) {
                return $this->_appendJs(
                    sprintf(
                        "<script>window.opener.socialRegisterCallback('%s','%s','%s','%s',window);</script>",
                        $type,
                        $userProfile->firstName . ' ' . $userProfile->lastName,
                        $userProfile->email,
                        $userProfile->phone
                    )
                );
            }
            $this->session->setSocialType(null)
                ->setActionType(null)
                ->setUserProfile(null);

            $this->refresh($customer);
            return $this->_appendJs();
        } catch (Exception $e) {
            $this->setBodyResponse($e->getMessage());
            return;
        }
    }

    /**
     * @param $userProfile
     * @param $type
     *
     * @return bool|Customer|mixed
     * @throws Exception
     * @throws LocalizedException
     */
    public function createCustomerProcess($userProfile, $type)
    {
        $name = explode(' ', $userProfile->displayName ?: __('New User'));

        $user = array_merge(
            [
                'email'      => $userProfile->email ?: $userProfile->identifier . '@' . strtolower($type) . '.com',
                'firstname'  => $userProfile->firstName ?: (array_shift($name) ?: $userProfile->identifier),
                'lastname'   => $userProfile->lastName ?: (array_shift($name) ?: $userProfile->identifier),
                'telephone'  => isset($userProfile->phone) ? $userProfile->phone : '',
                'identifier' => $userProfile->identifier,
                'type'       => $type,
                'password'   => isset($userProfile->password) ? $userProfile->password : null
            ],
            $this->getUserData($userProfile)
        );

        return $this->createCustomer($user, $type);
    }

    /**
     * Return javascript to redirect when login success
     *
     * @param null $content
     *
     * @return Raw
     */
    public function _appendJs($content = null)
    {
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        if ($this->_loginPostRedirect()) {
            $raw = $resultRaw->setContents(
                $content ?: sprintf(
                    "<script>window.opener.socialCallback('%s', window);</script>",
                    $this->_loginPostRedirect()
                )
            );
        } else {
            $raw = $resultRaw->setContents($content ?:
                "<script>
                    window.opener.document.write(\"<script>require(['jquery','domReady!'], function($) {\$(document).trigger('customer:login'); });<\/script>\");
                    window.opener.location.reload(true);
                    window.close();
                </script>");
        }

        return $raw;
    }

    /**
     * @param $customer
     *
     * @throws InputException
     * @throws FailureToSendException
     */
    public function refresh($customer)
    {
        if ($customer && $customer->getId()) {
            $this->session->loginById($customer->getId());
            $this->session->regenerateId();

            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @return     PhpCookieManager
     * @deprecated
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(
                PhpCookieManager::class
            );
        }

        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @return     CookieMetadataFactory
     * @deprecated
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(
                CookieMetadataFactory::class
            );
        }

        return $this->cookieMetadataFactory;
    }
}

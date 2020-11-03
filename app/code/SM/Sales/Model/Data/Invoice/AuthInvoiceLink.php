<?php
/**
 * Class HandleInvoiceLink
 * @package SM\Sales\Model\Data\Invoice
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model\Data\Invoice;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Integration\Api\IntegrationServiceInterface;
use Magento\Framework\Webapi\Request;
use Magento\Framework\Stdlib\DateTime\DateTime as Date;
use Magento\Integration\Helper\Oauth\Data as OauthHelper;

class AuthInvoiceLink
{
    /**
     * @var Token
     */
    protected $tokenFactory;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var OauthHelper
     */
    private $oauthHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * AuthInvoiceLink constructor.
     * @param TokenFactory $tokenFactory
     * @param Session $customerSession
     * @param Date|null $date
     * @param OauthHelper|null $oauthHelper
     */
    public function __construct(
        TokenFactory $tokenFactory,
        Session $customerSession,
        Date $date = null,
        OauthHelper $oauthHelper = null
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->customerSession = $customerSession;
        $this->date = $date ?: ObjectManager::getInstance()->get(
            Date::class
        );
        $this->oauthHelper = $oauthHelper ?: ObjectManager::getInstance()->get(
            OauthHelper::class
        );
    }

    /**
     * @return bool
     */
    public function authorization($request)
    {
        if ($tokenKey = $request->getParam('token')) {
            $token = $this->tokenFactory->create()->loadByToken($tokenKey);
            if (!$token->getId() || $token->getRevoked() || $this->isTokenExpired($token)) {
                return false;
            }
            $this->customerSession->setCustomerTokenId($token->getCustomerId());
        }

        return true;
    }

    /**
     * Check if token is expired.
     *
     * @param Token $token
     * @return bool
     */
    private function isTokenExpired(Token $token): bool
    {
        if ($token->getUserType() == UserContextInterface::USER_TYPE_ADMIN) {
            $tokenTtl = $this->oauthHelper->getAdminTokenLifetime();
        } elseif ($token->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER) {
            $tokenTtl = $this->oauthHelper->getCustomerTokenLifetime();
        } else {
            // other user-type tokens are considered always valid
            return false;
        }

        if (empty($tokenTtl)) {
            return false;
        }

        if (strtotime($token->getCreatedAt()) < ($this->date->gmtTimestamp() - $tokenTtl * 3600)) {
            return true;
        }

        return false;
    }
}

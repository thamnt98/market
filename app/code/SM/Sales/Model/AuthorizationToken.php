<?php
/**
 * Class AuthorizationToken
 * @package SM\Sales\Model
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Sales\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime as Date;
use Magento\Integration\Helper\Oauth\Data as OauthHelper;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\TokenFactory;

class AuthorizationToken
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
     * AuthInvoiceLink constructor.
     * @param TokenFactory $tokenFactory
     * @param Date|null $date
     * @param OauthHelper|null $oauthHelper
     */
    public function __construct(
        TokenFactory $tokenFactory,
        Date $date = null,
        OauthHelper $oauthHelper = null
    ) {
        $this->tokenFactory = $tokenFactory;
        $this->date = $date ?: ObjectManager::getInstance()->get(
            Date::class
        );
        $this->oauthHelper = $oauthHelper ?: ObjectManager::getInstance()->get(
            OauthHelper::class
        );
    }

    /**
     * @param $request
     * @param null $tokenTtl
     * @return bool
     */
    public function authorization($request, $tokenTtl = null): bool
    {
        if ($tokenKey = $request->getParam('token')) {
            $token = $this->tokenFactory->create()->loadByToken($tokenKey);
            if (!$token->getId() || $token->getRevoked() || $this->isTokenExpired($token, $tokenTtl)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if token is expired.
     *
     * @param Token $token
     * @param null $tokenTtl
     * @return bool
     */
    protected function isTokenExpired(Token $token, $tokenTtl = null): bool
    {
        if ($token->getUserType() == UserContextInterface::USER_TYPE_ADMIN) {
            $tokenTtl = $this->oauthHelper->getAdminTokenLifetime();
        } elseif ($token->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER && empty($tokenTtl)) {
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

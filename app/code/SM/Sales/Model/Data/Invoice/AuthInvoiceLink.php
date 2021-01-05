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

use Magento\Customer\Model\Session;
use Magento\Integration\Model\Oauth\Token;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Framework\Stdlib\DateTime\DateTime as Date;
use Magento\Integration\Helper\Oauth\Data as OauthHelper;

class AuthInvoiceLink extends \SM\Sales\Model\AuthorizationToken
{
    /**
     * @var Token
     */
    protected $tokenFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * AuthInvoiceLink constructor.
     * @param TokenFactory $tokenFactory
     * @param Date|null $date
     * @param OauthHelper|null $oauthHelper
     * @param Session $customerSession
     */
    public function __construct(
        TokenFactory $tokenFactory,
        Session $customerSession,
        Date $date = null,
        OauthHelper $oauthHelper = null
    ) {
        $this->customerSession = $customerSession;
        parent::__construct(
            $tokenFactory,
            $date,
            $oauthHelper
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
            if (!$token->getId() || $token->getRevoked() || $this->isTokenExpired($token)) {
                return false;
            }
            $this->customerSession->setCustomerTokenId($token->getCustomerId());
        }

        return true;
    }
}

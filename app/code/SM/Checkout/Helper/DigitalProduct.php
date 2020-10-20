<?php
/**
 * Class DigitalProduct
 * @package SM\Chekcout\
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

declare(strict_types=1);

namespace SM\Checkout\Helper;

class DigitalProduct
{
    const DIGITAL_FRONT_NAME = 'digitalproduct';
    const FROM_DIGITAL_SESSION_NAME = 'from_digital';
    const DIGITAL_FULL_ACTION_NAME = 'transcheckout_digitalproduct_index';

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var bool
     */
    private $isFromDigital;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Quote constructor.
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\Session\SessionManager $session
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Session\SessionManager $session,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->redirect = $redirect;
        $this->url = $url;
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * @return bool
     */
    public function isFromDigital()
    {
        if (empty($this->isVirtual)) {
            if (!$this->isAjax()) {
                if ($this->request->getFullActionName() == self::DIGITAL_FULL_ACTION_NAME) {
                    $this->isFromDigital = true;
                }
            }
        }

        return (bool) $this->isFromDigital;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        $isLiveChat = strpos($this->request->getRequestUri(), "livechat");
        if ($isLiveChat) {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return $this->request->isAjax();
    }

    /**
     * @return bool|null
     */
    public function getIsDigitalSession()
    {
        return $this->session->getData(self::FROM_DIGITAL_SESSION_NAME);
    }

    /**
     * @param $value
     * @return bool|null
     */
    public function setIsDigitalSession($value)
    {
        $this->session->setData(self::FROM_DIGITAL_SESSION_NAME, $value);
        return $this->getIsDigitalSession();
    }
}

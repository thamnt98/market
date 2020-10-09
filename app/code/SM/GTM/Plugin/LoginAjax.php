<?php

namespace SM\GTM\Plugin;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Session\SessionManagerInterface as CoreSession;

class LoginAjax
{

    /**
     * @var Json
     */
    private $helper;
    /**
     * @var CoreSession
     */
    private $coreSession;

    /**
     * LoginAjax constructor.
     * @param Json $helper
     * @param CoreSession $coreSession
     */
    public function __construct(
        Json $helper,
        CoreSession $coreSession
    ) {
        $this->helper = $helper;
        $this->coreSession = $coreSession;
    }

    /**
     * @param \SM\Customer\Controller\Trans\LoginAjax $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\SM\Customer\Controller\Trans\LoginAjax $subject, $result)
    {
        $credentials = $this->helper->unserialize($subject->getRequest()->getContent());
        $username = $credentials['username'];

        if ($this->isEmail($username)) {
            $this->coreSession->setLoginTypeGtm('Email');
        } else {
            $this->coreSession->setLoginTypeGtm('Phone Number');
        }
        return $result;
    }

    /**
     * @param $username
     * @return false|int
     */
    private function isEmail($username)
    {
        return preg_match("/[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+.[a-zA-Z]{2,4}/", $username);
    }
}

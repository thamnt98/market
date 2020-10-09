<?php

namespace SM\Customer\Block\SocialLogin\Popup;

use Magento\Framework\View\Element\Template\Context;
use Mageplaza\SocialLogin\Block\Popup\Social as BasePopupSocialLogin;
use Mageplaza\SocialLogin\Helper\Social as SocialHelper;
use Mageplaza\SocialLogin\Model\System\Config\Source\Position;

/**
 * Class Social
 * @package SM\Customer\Block\SocialLogin\Popup
 */
class Social extends BasePopupSocialLogin
{
    const SOCIAL_LOGIN_ACTION_LOGIN = 'login';
    const SOCIAL_LOGIN_ACTION_REGISTER = 'register';

    private $actionType;

    /*public function __construct(
        Context $context,
        SocialHelper $socialHelper,
        array $data = [])
    {
        parent::__construct($context, $socialHelper, $data);
        var_dump($data);
        $this->actionType = $this->getActionType() ? : self::SOCIAL_LOGIN_ACTION_LOGIN;
    }*/

    /**
     * @return array
     */
    public function getAvailableSocials()
    {
        $availabelSocials = [];

        foreach ($this->socialHelper->getSocialTypes() as $socialKey => $socialLabel) {
            $this->socialHelper->setType($socialKey);
            if ($this->socialHelper->isEnabled()) {
                $availabelSocials[$socialKey] = [
                    'label'     => $socialLabel,
                    'login_url' => $this->getLoginUrl($socialKey),
                ];
            }
        }

        return $availabelSocials;
    }

    /**
     * @param $key
     *
     * @return string
     */
    public function getBtnKey($key)
    {
        switch ($key) {
            case 'vkontakte':
                $class = 'vk';
                break;
            default:
                $class = $key;
        }

        return $class;
    }

    /**
     * @return array
     */
    public function getSocialButtonsConfig()
    {
        $availableButtons = $this->getAvailableSocials();
        foreach ($availableButtons as $key => &$button) {
            $button['url']     = $this->getLoginUrl($key, ['authen' => 'popup']);
            $button['key']     = $key;
            $button['btn_key'] = $this->getBtnKey($key);
        }

        return $availableButtons;
    }

    /**
     * @param null $position
     *
     * @return bool
     */
    public function canShow($position = null)
    {
        $displayConfig = $this->socialHelper->getConfigGeneral('social_display');
        $displayConfig = explode(',', $displayConfig);

        if (!$position) {
            $position = $this->getRequest()->getFullActionName() === 'customer_account_login' ?
                Position::PAGE_LOGIN :
                Position::PAGE_CREATE;
        }

        return in_array($position, $displayConfig);
    }

    /**
     * @param $socialKey
     * @param array $params
     *
     * @return string
     */
    public function getLoginUrl($socialKey, $params = [])
    {
        $params['type'] = $socialKey;
        $params['action-type'] = $this->getActionType();

        return $this->getUrl('sociallogin/social/login', $params);
    }

    public function setActionTypeIsLogin()
    {
        $this->actionType = self::SOCIAL_LOGIN_ACTION_LOGIN;
    }

    public function setActionTypeIsRegister()
    {
        $this->actionType = self::SOCIAL_LOGIN_ACTION_REGISTER;
    }

    public function setActionType($actionType)
    {
        $this->actionType = $actionType;
    }

    public function getActionType()
    {
        return $this->actionType;
    }
}

<?php

namespace SM\Customer\Controller\Social;

/**
 * Class Callback
 * @package SM\Customer\Controller\Social
 */
class Callback extends \Mageplaza\SocialLogin\Controller\Social\Callback
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $hybrid = new \Hybrid_Endpoint();
        $param = $this->getRequest()->getParams();
        if (isset($param['live.php'])) {
            $request = array_merge($param, ['hauth_done' => 'Live']);
        }
        if ($this->checkRequest('hauth_start', false)
            && (($this->checkRequest('error_reason', 'user_denied')
                    && $this->checkRequest('error', 'access_denied')
                    && $this->checkRequest('error_code', '200')
                    && $this->checkRequest('hauth_done', 'Facebook'))
                || ($this->checkRequest('hauth_done', 'Twitter') && $this->checkRequest('denied')))
        ) {
            return $this->_appendJs(sprintf('<script>window.close();</script>'));
        }
        if (isset($request)) {
            $hybrid->process($request);
        }

        $hybrid->process();
    }
}

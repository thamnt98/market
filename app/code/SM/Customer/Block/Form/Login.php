<?php

namespace SM\Customer\Block\Form;

use Magento\Customer\Model\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Block\Switcher;
use Magento\Store\Model\Store;
use SM\Help\Api\QuestionRepositoryInterface;

/**
 * Class Login
 * @package SM\Customer\Block\Form
 */
class Login extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_TERMS_CONDITIONS = 'sm_help/show_form_login_terms_privacy/terms_conditions';
    const XML_PATH_PRIVACY_POLICY = 'sm_help/show_form_login_terms_privacy/privacy_policy';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var QuestionRepositoryInterface
     */
    protected $questionRepository;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $cookiePersistent;
    protected $helperFlags;

    protected $switcherBlock;
    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * Login constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param QuestionRepositoryInterface $questionRepository
     * @param Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Persistent\Helper\Session $cookiePersistent
     * @param \SM\StoreviewFlags\Helper\Data $helperFlags
     * @param Switcher $switcherBlock
     * @param EncoderInterface $encoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        QuestionRepositoryInterface $questionRepository,
        Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Persistent\Helper\Session $cookiePersistent,
        \SM\StoreviewFlags\Helper\Data $helperFlags,
        Switcher $switcherBlock,
        EncoderInterface $encoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->cookiePersistent = $cookiePersistent;
        $this->helperFlags = $helperFlags;
        $this->switcherBlock = $switcherBlock;
        $this->encoder = $encoder;
        $this->request = $request;
        $this->questionRepository = $questionRepository;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);

        /*need recheck with Persistent*/
        /*$customerGroup = $this->httpContext->getValue(Context::CONTEXT_GROUP);
        $loggedIn = $customerGroup ?? ($customerGroup ?? null);
        if ($this->cookiePersistent->isPersistent()) {
            $persistent = $this->cookiePersistent->getSession();
            $loggedIn = $loggedIn ?: $persistent->getGroupId();
        }
        return $loggedIn;*/
    }

    /**
     * @return Template
     */
    protected function _prepareLayout()
    {
        if ($this->isLoggedIn()) {
            $this->pageConfig->addBodyClass('login');
        } else {
            $this->pageConfig->addBodyClass('logout');
        }
        return parent::_prepareLayout();
    }

    /**
     * Get login url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('customer/account/loginPost');
    }

    /**
     * Get Ajax login url
     *
     * @return string
     */
    public function getAjaxLoginUrl()
    {
        return $this->getUrl('customer/trans/loginAjax');
    }

    /**
     * Get prepared social login buttons
     *
     * @return array;
     */
    public function getPreparedButtons()
    {
        // TODO: Add Buttons
        return [];
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param null $storeId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreviewFlags($storeId = null)
    {
        return $this->getMediaUrl() . 'smstoresflags/' . $this->helperFlags->getFlagUpload($storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getAbbreviationName($storeId = null)
    {
        return $this->helperFlags->getAbbreviationName($storeId);
    }

    /**
     * Get current store code.
     *
     * @return string
     */
    public function getCurrentStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    public function getStoreUrl($storeCode)
    {
        return $this->_storeManager->getStore()->getUrl() . '?___store=' . $storeCode;
    }

    /**
     * Get stores.
     *
     * @return \Magento\Store\Model\Store[]
     */
    public function getStores()
    {
        return $this->switcherBlock->getStores();
    }

    /**
     * Get current Store Id.
     *
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getTargetStoreRedirectUrl(Store $store): string
    {
        return $this->getUrl(
            'stores/store/redirect',
            [
                '___store' => $store->getCode(),
                '___from_store' => $this->_storeManager->getStore()->getCode(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->encoder->encode(
                    $store->getCurrentUrl(false)
                ),
            ]
        );
    }


    /**
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isShowLogin()
    {
        $topicId = $this->request->getParam('topic_id');
        $questionId = $this->request->getParam('question_id');
        if ($topicId) {
            if ($topicId == $this->getTermCondition() || $topicId == $this->getPrivacyPolicy()) {
                return true;
            }
        }
        if ($questionId) {
            if ($this->getTopicId($questionId) == $this->getTermCondition()
                || $this->getTopicId($questionId) == $this->getPrivacyPolicy()) {
                return true;
            }
        }
        return $this->isLoggedIn();
    }

    /**
     *
     * @param $value
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getTopicId($value)
    {
        $question = $this->questionRepository->getById($value);
        return $question->getTopicId();

    }

    protected function getTermCondition()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_TERMS_CONDITIONS
        );
    }

    protected function getPrivacyPolicy()
    {
        return $this->_scopeConfig->getValue(
            self::XML_PATH_PRIVACY_POLICY
        );
    }
}
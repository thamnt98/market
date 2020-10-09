<?php

namespace SM\Customer\Block\Form;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Register extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $cookiePersistent;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * @var \SM\Customer\Helper\Config
     */
    protected $config;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * Register constructor.
     * @param \SM\Customer\Helper\Config $config
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Api\BlockRepositoryInterface $blockRepository
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Persistent\Helper\Session $cookiePersistent
     * @param JsonHelper $jsonHelper
     * @param array $data
     */
    public function __construct(
        \SM\Customer\Helper\Config $config,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Persistent\Helper\Session $cookiePersistent,
        JsonHelper $jsonHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->cookiePersistent = $cookiePersistent;
        $this->_jsonHelper = $jsonHelper;
        $this->config = $config;
        $this->filterProvider = $filterProvider;
        $this->blockRepository = $blockRepository;
    }

    /**
     * Checking customer register status
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
     * Get prepared social register buttons
     *
     * @return array;
     */
    public function getButtons()
    {
        // TODO: Add Buttons
        return [];
    }

    /**
     * Get register url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('customer/trans/createAccount');
    }

    /**
     * Get minimum password length
     *
     * @return string
     * @since 100.1.0
     */
    public function getMinimumPasswordLength()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get number of password required character classes
     *
     * @return string
     * @since 100.1.0
     */
    public function getRequiredCharacterClassesNumber()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    private function jsonEncode($valueToEncode)
    {
        try {
            $encodeValue = $this->_jsonHelper->jsonEncode($valueToEncode);
        } catch (Exception $e) {
            $encodeValue = '{}';
        }

        return $encodeValue;
    }

    /**
     * @return string
     */
    public function getTermsConditionsContent()
    {
        try {
            $id = $this->config->getTermsConditions();
            if (!$id) {
                return '';
            }
            $block = $this->blockRepository->getById($id);
            return $this->filterProvider->getBlockFilter()->filter($block->getContent());
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getPrivacyPolicyContent()
    {
        try {
            $id = $this->config->getPrivacyPolicy();
            if (!$id) {
                return '';
            }
            $block = $this->blockRepository->getById($id);
            return $this->filterProvider->getBlockFilter()->filter($block->getContent());
        } catch (\Exception $e) {
            return '';
        }
    }
}

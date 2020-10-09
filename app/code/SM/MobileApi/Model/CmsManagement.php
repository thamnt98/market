<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\MobileApi\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\PageFactory;

/**
 * Class CmsManagement
 * @package SM\MobileApi\Model
 */
class CmsManagement implements \SM\MobileApi\Api\CmsManagementInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    protected $CMSPageHelper;

    protected $pageFactory;

    /**
     * CmsManagement constructor.
     * @param \SM\MobileApi\Helper\CMSPage $CMSPageHelper
     * @param StoreManagerInterface $storeManager
     * @param PageFactory $pageFactory
     */
    public function __construct(
        \SM\MobileApi\Helper\CMSPage $CMSPageHelper,
        StoreManagerInterface $storeManager,
        PageFactory $pageFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->CMSPageHelper = $CMSPageHelper;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTermsConditionsUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl() . self::TERMS_CONDITIONS_URL ?? '';
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPrivacyPolicyUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl() . self::PRIVACY_POLICY_URL ?? '';
    }

    public function getAboutUsUrl(){
        return $this->storeManager->getStore()->getBaseUrl() . self::ABOUT_US ?? '';

    }

    public function getContentById($pageId)
    {
        $page = $this->pageFactory->create();
        $page->load($pageId);
        if (!$page->getId()) {
            throw new NoSuchEntityException(__('The CMS page with the "%1" ID doesn\'t exist.', $pageId));
        }

        $data = [
            "title" => $page->getTitle(),
            "description" => $page->getMetaDescription()
        ];
        return $data;
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCmsPages()
    {
        $result = [];
        $result[] = ['name' => 'privacy_policy',
            'url' => $this->getPrivacyPolicyUrl(),
            'title' => $this->getContentById($this->CMSPageHelper->getPolicyPageId())['title'],
            'description' => $this->getContentById($this->CMSPageHelper->getPolicyPageId())['description']
        ];
        $result[] = ['name' => 'terms_conditions',
            'url' => $this->getTermsConditionsUrl() ,
            'title' => $this->getContentById($this->CMSPageHelper->getTermsPageId())['title'],
            'description' => $this->getContentById($this->CMSPageHelper->getTermsPageId())['description']];
        $result[] = ['name' => 'about_us',
            'url' => $this->getAboutUsUrl(),
            'title' => $this->getContentById($this->CMSPageHelper->getAboutUsId())['title'],
            'description' => $this->getContentById($this->CMSPageHelper->getAboutUsId())['description']];

        return $result;
    }
}

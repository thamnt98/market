<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Plugin;

use Amasty\Shopby\Helper\UrlBuilder;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class UrlBuilderPlugin
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     *
     * @param \Magento\Framework\Registry $registry
     * @param Http                        $request
     * @param StoreManagerInterface       $storeManager
     * @param CategoryRepository          $categoryRepository
     * @param ScopeConfigInterface        $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        Http $request,
        StoreManagerInterface $storeManager,
        CategoryRepository $categoryRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request            = $request;
        $this->storeManager       = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->scopeConfig        = $scopeConfig;
        $this->registry = $registry;
    }

    /**
     * @param UrlBuilder $subject
     * @param $result
     * @return string
     */
    public function afterBuildUrl(UrlBuilder $subject, $result)
    {
        try {
            $value = $this->request->getParam($this->getBrandAttributeCode());

            $url   = explode('/', $result);
            $page  = explode('?', $url[3]);
            $query = explode('=', $page[1]);

            if (!$this->registry->registry('current_category') && $query[0] === 'cat') {
                $category = $this->categoryRepository->get($query[1], $this->storeManager->getStore()->getId());
                return $category->getUrl() . '?brand=' . $value;
            }
        } catch (\Exception $e) {
        }
        return $result;
    }

    /**
     * @return string
     */
    protected function getBrandAttributeCode()
    {
        return $this->scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
            ScopeInterface::SCOPE_STORE
        );
    }
}

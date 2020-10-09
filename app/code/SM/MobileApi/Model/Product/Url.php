<?php

namespace SM\MobileApi\Model\Product;

/**
 * Class Url
 * @package SM\MobileApi\Model\Product
 */
class Url
{
    protected $storeManager;
    protected $japiProductHelper;
    protected $urlFinder;
    protected $objectManager;

    /**
     * Url constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \SM\MobileApi\Helper\Product $japiProductHelper
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \SM\MobileApi\Helper\Product $japiProductHelper
    ) {
        $this->storeManager      = $storeManager;
        $this->japiProductHelper = $japiProductHelper;
        $this->urlFinder         = $urlFinder;
        $this->objectManager     = $objectManager;
    }

    /**
     * @param string $url
     *
     * @return null|\SM\MobileApi\Api\Data\Product\ProductDetailsInterface
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function getProduct($url)
    {
        $uri = \Zend\Uri\UriFactory::factory($url);
        if (! $uri->isValid()) {
            throw new \Magento\Framework\Webapi\Exception(
                __('Invalid URI provided to constructor'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->objectManager->create('Magento\Framework\App\Request\Http', [
            'uri' => $uri->getPath()
        ]);

        $rewrite = $this->urlFinder->findOneByData([
            \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::REQUEST_PATH => ltrim($request->getPathInfo(), '/'),
            \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::STORE_ID     => $this->storeManager->getStore()->getId(),
        ]);

        $productId = null;
        if ($rewrite) {
            if ($rewrite->getEntityType() == \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite::ENTITY_TYPE_PRODUCT) {
                $productId = $rewrite->getEntityId();
            }
        }

        return $this->japiProductHelper->convertProductDetailsToResponseV2($productId);
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public function parseUrl($url)
    {
        $data = [];

        $uri = \Zend\Uri\UriFactory::factory($url);
        if (! $uri->isValid()) {
            return $data;
        }

        /** @var \Magento\Framework\App\Request\Http $request */
        $request = $this->objectManager->create('Magento\Framework\App\Request\Http', [
            'uri' => $uri->getPath()
        ]);

        $rewrite = $this->urlFinder->findOneByData([
            \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::REQUEST_PATH => ltrim($request->getPathInfo(), '/'),
            \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::STORE_ID     => $this->storeManager->getStore()->getId(),
        ]);

        if ($rewrite) {
            if ($rewrite->getEntityType() == \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite::ENTITY_TYPE_PRODUCT) {
                $data['type'] = 'product';
                $data['id']   = $rewrite->getEntityId();
            } elseif ($rewrite->getEntityType() == \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite::ENTITY_TYPE_CATEGORY) {
                $data['type'] = 'category';
                $data['id']   = $rewrite->getEntityId();
            }
        }

        return $data;
    }
}

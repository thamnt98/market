<?php
/**
 * SM\Review\Helper
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\Review\Helper;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package SM\Review\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Data constructor.
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param UrlInterface $urlInterface
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepository $productRepository
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        UrlInterface $urlInterface,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->urlInterface = $urlInterface;
        $this->timezone = $timezone;
        parent::__construct($context);
    }

    /**
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->timezone->date($date)->format('d M Y | H:i A');
    }

    /**
     * @param string $path
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl($path)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . "catalog/product" . $path;
    }

    /**
     * @param int[] $productIds
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts($productIds)
    {
        $criteria = $this->searchCriteriaBuilder
            ->addFilter("entity_id", $productIds, "in")
            ->create();
        return $this->productRepository->getList($criteria)->getItems();
    }
}

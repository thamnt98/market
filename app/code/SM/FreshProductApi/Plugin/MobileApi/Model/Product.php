<?php
/**
 * SM\FreshProductApi\Plugin\MobileApi\Model
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\FreshProductApi\Plugin\MobileApi\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\MobileApi\Api\Data\Product\ListInterface;

/**
 * Class Product
 * @package SM\FreshProductApi\Plugin\MobileApi\Model
 */
class Product
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Product constructor.
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \SM\MobileApi\Model\Product $subject
     * @param \SM\MobileApi\Api\Data\Product\ListInterface $result
     * @param int $category_id
     * @return ListInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetList($subject, $result, $category_id) {
        $category = $this->categoryRepository->get(
            $category_id,
            $this->storeManager->getStore()->getId()
        );
        $result
            ->setIsFresh($category->getData(ListInterface::IS_FRESH) ?? false);

        return $result;
    }
}

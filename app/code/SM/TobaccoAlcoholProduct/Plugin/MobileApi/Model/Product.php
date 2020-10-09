<?php
/**
 * SM\TobaccoAlcoholProduct\Plugin\MobileApi\Model
 *
 * @copyright Copyright © 2020 SmartOSC. All rights reserved.
 * @author    hungnv6@smartosc.com
 */

namespace SM\TobaccoAlcoholProduct\Plugin\MobileApi\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\MobileApi\Model\Product as BaseProduct;
use SM\MobileApi\Api\Data\Product\ListInterface;

/**
 * Class Product
 * @package SM\TobaccoAlcoholProduct\Plugin\MobileApi\Model
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
     * @param BaseProduct $subject
     * @param ListInterface $result
     * @param int $category_id
     * @return ListInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetList($subject, $result, $category_id)
    {
        $category = $this->categoryRepository->get(
            $category_id,
            $this->storeManager->getStore()->getId()
        );
        $result
            ->setIsAlcohol($category->getData(ListInterface::IS_ALCOHOL))
            ->setIsTobacco($category->getData(ListInterface::IS_TOBACCO));
        return $result;
    }
}

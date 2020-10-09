<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Api;

/**
 * Interface CategoryRepositoryInterface
 * @package SM\DigitalProduct\Api
 */
interface CategoryRepositoryInterface
{

    /**
     * Save category
     * @param \SM\DigitalProduct\Api\Data\CategoryInterface $category
     * @return \SM\DigitalProduct\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($category);

    /**
     * Retrieve category
     * @param int $categoryId
     * @param int $storeId
     * @return \SM\DigitalProduct\Api\Data\CategoryInterface
     */
    public function get($categoryId, $storeId);

    /**
     * Retrieve category matching the specified criteria.
     * @return \SM\DigitalProduct\Api\Data\CategoryInterface[]
     */
    public function getList();

    /**
     * Delete category
     * @param \SM\DigitalProduct\Api\Data\CategoryInterface $category
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($category);

    /**
     * Delete category by ID
     * @param string $categoryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($categoryId);

    /**
     * @param string $digitalCatCode
     * @return \SM\DigitalProduct\Api\Data\CategoryContentInterface
     */
    public function getCategoryContent($digitalCatCode);

}


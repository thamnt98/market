<?php

namespace Mirasvit\Blog\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Filter\FilterManager;
use Mirasvit\Blog\Api\Data\PostInterface;
use Mirasvit\Blog\Model\Config;
use Mirasvit\Blog\Model\TagFactory as TagModelFactory;

class Post extends AbstractEntity
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FilterManager
     */
    protected $filter;

    /**
     * @var TagModelFactory
     */
    protected $tagFactory;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $productFactory;

    /**
     * Post constructor.
     * @param \Magento\Catalog\Model\Product $productFactory
     * @param Config $config
     * @param TagModelFactory $tagFactory
     * @param FilterManager $filter
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Config $config,
        TagModelFactory $tagFactory,
        FilterManager $filter,
        Context $context,
        $data = []
    ) {
        $this->tagFactory = $tagFactory;
        $this->config     = $config;
        $this->filter     = $filter;

        parent::__construct($context, $data);
        $this->productFactory = $productFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Mirasvit\Blog\Model\Post::ENTITY);
        }

        return parent::getEntityType();
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(DataObject $post)
    {
        /** @var PostInterface $post */

        $post->setCategoryIds($this->getCategoryIds($post));
        $post->setStoreIds($this->getStoreIds($post));
        $post->setTagIds($this->getTagIds($post));
        $productIds = $this->getProductIds($post);
        $productIdsFilter = [];
        foreach ($productIds as $productId) {
            $product =$this->productFactory->create()->load($productId);
            if ($product->getId()) {
                $productIdsFilter[] = $productId;
            }
        }
        $post->setProductIds($productIdsFilter);
        $post->setData(\SM\InspireMe\Helper\Data::RELATED_PRODUCT_POSITION, $this->getProductPositions($post));
        $post->setData(\SM\InspireMe\Helper\Data::RELATED_PRODUCT_VALUE, $this->getProductValues($post));
        return parent::_afterLoad($post);
    }

    /**
     * @param PostInterface $model
     * @return int[]|null
     */
    protected function getProductPositions(PostInterface $model)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->getTable('mst_blog_post_product'),
            'position'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PostInterface $model
     * @return int[]|null
     */
    protected function getProductValues(PostInterface $model)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->getTable('mst_blog_post_product'),
            'value'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PostInterface $model
     * @return int[]|null
     */
    protected function getCategoryIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('mst_blog_category_post'),
            'category_id'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PostInterface $model
     * @return int[]|null
     */
    protected function getStoreIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('mst_blog_store_post'),
            'store_id'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PostInterface $model
     * @return int[]|null
     */
    protected function getTagIds(PostInterface $model)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->getTable('mst_blog_tag_post'),
            'tag_id'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PostInterface $model
     * @return int[]|null
     */
    protected function getProductIds(PostInterface $model)
    {
        $connection = $this->getConnection();
        $select     = $connection->select()->from(
            $this->getTable('mst_blog_post_product'),
            'product_id'
        )->where(
            'post_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(DataObject $post)
    {
        /** @var PostInterface $post */
        $this->saveCategoryIds($post);
        $this->saveStoreIds($post);
        $this->saveTagIds($post);
        $this->saveProductIds($post);

        return parent::_afterSave($post);
    }

    /**
     * @param PostInterface $model
     * @return $this
     */
    protected function saveCategoryIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('mst_blog_category_post');

        if (!$model->getCategoryIds()) {
            return $this;
        }

        $categoryIds    = $model->getCategoryIds();
        $oldCategoryIds = $this->getCategoryIds($model);

        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }

                $data[] = [
                    'category_id' => (int)$categoryId,
                    'post_id'     => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = ['post_id = ?' => (int)$model->getId(), 'category_id = ?' => (int)$categoryId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

    /**
     * @param PostInterface $model
     * @return $this
     */
    protected function saveStoreIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('mst_blog_store_post');

        /**
         * If store ids data is not declared we haven't do manipulations
         */
        if (!$model->getStoreIds()) {
            return $this;
        }

        $storeIds    = $model->getStoreIds();
        $oldStoreIds = $this->getStoreIds($model);

        $insert = array_diff($storeIds, $oldStoreIds);
        $delete = array_diff($oldStoreIds, $storeIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $storeId) {
                if (empty($storeId)) {
                    continue;
                }
                $data[] = [
                    'store_id' => (int)$storeId,
                    'post_id'  => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $storeId) {
                $where = ['post_id = ?' => (int)$model->getId(), 'store_id = ?' => (int)$storeId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

    /**
     * @param PostInterface $model
     * @return $this
     */
    protected function saveTagIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('mst_blog_tag_post');

        if (!$model->getTagIds()) {
            return $this;
        }

        $tagIds    = $model->getTagIds();
        $oldTagIds = $this->getTagIds($model);

        $insert = array_diff($tagIds, $oldTagIds);
        $delete = array_diff($oldTagIds, $tagIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $tagId) {
                if (empty($tagId)) {
                    continue;
                }
                $data[] = [
                    'tag_id'  => (int)$tagId,
                    'post_id' => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $tagId) {
                $where = ['post_id = ?' => (int)$model->getId(), 'tag_id = ?' => (int)$tagId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

    /**
     * @param PostInterface $model
     * @return $this
     */
    protected function saveProductIds(PostInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('mst_blog_post_product');

        $productIds    = $model->getProductIds();
        $oldProductIds = $this->getProductIds($model);

        $insert = array_diff($productIds, $oldProductIds);
        $delete = array_diff($oldProductIds, $productIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId) {
                if (empty($productId)) {
                    continue;
                }
                $data[] = [
                    'product_id' => (int)$productId,
                    'post_id'    => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $productId) {
                $where = ['post_id = ?' => (int)$model->getId(), 'product_id = ?' => (int)$productId];
                $connection->delete($table, $where);
            }
        }

        $productPositions = $model->getData(\SM\InspireMe\Helper\Data::RELATED_PRODUCT_POSITION);
        $productValues    = $model->getData(\SM\InspireMe\Helper\Data::RELATED_PRODUCT_VALUE);

        for ($cnt = 0; $cnt < count($productIds); $cnt++) {
            $where = [
                'post_id = ?' => (int)$model->getId(),
                'product_id = ?' => (int)$productIds[$cnt]
            ];
            $connection->update(
                $table,
                [
                    'position' => $productPositions[$cnt],
                    'value'    => $productValues[$cnt]
                ],
                $where
            );
        }

        return $this;
    }
}

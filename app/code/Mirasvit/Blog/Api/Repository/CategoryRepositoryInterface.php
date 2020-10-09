<?php

namespace Mirasvit\Blog\Api\Repository;

use Mirasvit\Blog\Api\Data\CategoryInterface;
use Mirasvit\Blog\Model\ResourceModel\Category\Collection;

interface CategoryRepositoryInterface
{
    /**
     * @return Collection | \Mirasvit\Blog\Api\Data\CategoryInterface[]
     */
    public function getCollection();

    /**
     * @return \Mirasvit\Blog\Api\Data\CategoryInterface
     */
    public function create();

    /**
     * @param CategoryInterface $model
     *
     * @return \Mirasvit\Blog\Api\Data\CategoryInterface
     */
    public function save(CategoryInterface $model);

    /**
     * @param int $id
     *
     * @return \Mirasvit\Blog\Api\Data\CategoryInterface|false
     */
    public function get($id);

    /**
     * @param \Mirasvit\Blog\Api\Data\CategoryInterface $model
     *
     * @return bool
     */
    public function delete(CategoryInterface $model);
}

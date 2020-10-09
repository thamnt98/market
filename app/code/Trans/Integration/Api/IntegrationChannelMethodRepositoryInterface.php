<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use \Trans\Integration\Api\Data\IntegrationChannelMethodInterface;

interface IntegrationChannelMethodRepositoryInterface
{
    /**
     * Retrieve data by id
     *
     * @param int $id
     * @return \Trans\Integration\Api\Data\IntegrationChannelMethodInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Save data.
     *
     * @param \Trans\Integration\Api\Data\IntegrationChannelMethodInterface $data
     * @return \Trans\Integration\Api\Data\IntegrationChannelMethodInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(IntegrationChannelMethodInterface $data);

     /**
     * Delete data.
     *
     * @param \Trans\Integration\Api\Data\IntegrationChannelMethodInterface $data
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(IntegrationChannelMethodInterface $data);

    /**
     * Status Active.
     *
     * @return \Trans\Integration\Api\Data\IntegrationChannelMethodInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByStatusActive();

    /**
     * @param $tag
     * @return mixed
     */
    public function getCollectionInTagByStatusActive($tag);

    /**
     * @param $tag
     * @return mixed
     */
    public function getItemInTagByStatusActive($tag);


}
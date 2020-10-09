<?php
/**
 * Class SearchResults
 * @package SM\Help\Api
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\Help\Api;

interface ContactRepositoryInterface
{
    /**
     * @param int $customerId
     * @param string[] $data
     * @param \Magento\Framework\Api\ImageContent[] $images
     * @return boolean
     */
    public function submit($customerId, $data, $images);

    /**
     * @return \SM\StoreLocator\Api\Data\StoreInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListStore();

    /**
     * @param \Magento\Framework\Api\ImageContent $imageContent
     * @return bool
     */
    public function uploadImage(\Magento\Framework\Api\ImageContent $imageContent);
}

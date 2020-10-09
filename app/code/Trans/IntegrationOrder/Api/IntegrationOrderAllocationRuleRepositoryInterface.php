<?php
/**
 * @category Trans
 * @package  Trans_IntegrationOrder
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@ctcorpdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationOrder\Api;

use Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface;

interface IntegrationOrderAllocationRuleRepositoryInterface
{

    /**
     * Save data.
     *
     * @param \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface $data
     * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(IntegrationOrderAllocationRuleInterface $data);

    /**
     * Retrieve data by id
     *
     * @param int $oarId
     * @return \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($oarId);

    /**
     * Retrieve data by quote id
     *
     * @param  \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface  $quoteId
     * @return  \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataByQuoteId(IntegrationOrderAllocationRuleInterface $quoteId);

    /**
     * Retrieve data by address and quote id
     *
     * @param  $quoteId
     * @param  $addressId
     * @return  \Trans\IntegrationOrder\Api\Data\IntegrationOrderAllocationRuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadDataByAddressQuoteId($quoteId, $addressId);
}

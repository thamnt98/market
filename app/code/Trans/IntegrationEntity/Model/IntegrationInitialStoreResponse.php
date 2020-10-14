<?php
/**
 * @category Trans
 * @package  Trans_IntegrationEntity
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationEntity\Model;

use Trans\IntegrationEntity\Api\Data\IntegrationInitialStoreResponseInterface;
 
/**
 * Class InventoryPosUpdateResponse
 */
class IntegrationInitialStoreResponse extends \Magento\Framework\Model\AbstractExtensibleModel implements IntegrationInitialStoreResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(IntegrationInitialStoreResponseInterface::MESSAGE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(IntegrationInitialStoreResponseInterface::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(IntegrationInitialStoreResponseInterface::CODE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(IntegrationInitialStoreResponseInterface::CODE, $code);
    }
}
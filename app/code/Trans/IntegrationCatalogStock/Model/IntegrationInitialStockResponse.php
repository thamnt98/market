<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Model;

use Trans\IntegrationCatalogStock\Api\Data\IntegrationInitialStockResponseInterface;

/**
 * Class InventoryPosUpdateResponse
 */
class IntegrationInitialStockResponse extends \Magento\Framework\Model\AbstractExtensibleModel implements IntegrationInitialStockResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(IntegrationInitialStockResponseInterface::MESSAGE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(IntegrationInitialStockResponseInterface::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(IntegrationInitialStockResponseInterface::CODE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(IntegrationInitialStockResponseInterface::CODE, $code);
    }
}
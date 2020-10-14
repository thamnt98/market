<?php
/**
 * @category Trans
 * @package  Trans_CatalogStock
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *  @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\IntegrationCatalogStock\Model;

use Trans\IntegrationCatalogStock\Api\Data\IntegrationCheckStockResponseInterface;

/**
 * Class InventoryPosUpdateResponse
 */
class IntegrationCheckStockResponse extends \Magento\Framework\Model\AbstractExtensibleModel implements IntegrationCheckStockResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(IntegrationCheckStockResponseInterface::MESSAGE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(IntegrationCheckStockResponseInterface::MESSAGE, $message);
    }

     /**
     * {@inheritdoc}
     */
    public function getDatas()
    {
        return $this->getData(IntegrationCheckStockResponseInterface::DATA);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setDatas($data)
    {
        return $this->setData(IntegrationCheckStockResponseInterface::DATA, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->getData(IntegrationCheckStockResponseInterface::CODE);
    }
 
    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(IntegrationCheckStockResponseInterface::CODE, $code);
    }
}
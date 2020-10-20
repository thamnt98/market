<?php

namespace SM\Checkout\Model\Api\Cart;

use Magento\Framework\Model\AbstractExtensibleModel;
use SM\Checkout\Api\Data\CartItem\UpdateItemInterface;

class UpdateItem extends AbstractExtensibleModel implements UpdateItemInterface
{
    /**
     * @inheritdoc
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setItemId($data)
    {
        return $this->setData(self::ITEM_ID, $data);
    }

    /**
     * @inheritdoc
     */
    public function getIsChecked()
    {
        return $this->getData(self::IS_CHECKED);
    }

    /**
     * @inheritdoc
     */
    public function setIsChecked($data)
    {
        return $this->setData(self::IS_CHECKED,$data);
    }
}

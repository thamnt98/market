<?php

namespace SM\MobileApi\Model\Data\Promo;

use Magento\Framework\Api\AbstractExtensibleObject;
use SM\MobileApi\Api\Data\Promo\PromoListingInterface;

class PromoListing extends AbstractExtensibleObject implements PromoListingInterface
{

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getImageUrl()
    {
        return $this->_get(self::IMAGE_URL);
    }

    /**
     * @inheritDoc
     */
    public function setImageUrl($imageUrl)
    {
        return $this->setData(self::IMAGE_URL, $imageUrl);
    }
}

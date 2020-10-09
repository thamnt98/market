<?php

namespace SM\MobileApi\Model\Data\Store;

use SM\MobileApi\Api\Data\Store\StoreViewInterface;

/**
 * Class StoreView
 *
 * @package SM\MobileApi\Model\Data\Store
 */
class StoreView extends \Magento\Framework\Model\AbstractExtensibleModel implements StoreViewInterface
{
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    public function setStoreId($id)
    {
        return $this->setData(self::STORE_ID, $id);
    }

    public function getStoreCode()
    {
        return $this->getData(self::STORE_CODE);
    }

    public function setStoreCode($code)
    {
        return $this->setData(self::STORE_CODE, $code);
    }

    public function getLanguage()
    {
        return $this->getData(self::LANGUAGE);
    }

    public function setLanguage($language)
    {
        return $this->setData(self::LANGUAGE, $language);
    }
}

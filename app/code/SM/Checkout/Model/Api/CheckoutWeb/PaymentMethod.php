<?php

namespace SM\Checkout\Model\Api\CheckoutWeb;

class PaymentMethod extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\PaymentMethodInterface
{
    const TITLE = 'title';
    const STORE_ID = 'store_id';
    const IS_ACTIVE = 'is_active';
    const DESCRIPTION= 'description';
    const TOOLTIP_DESCRIPTION = 'tooltip_description';
    const LOGO = 'logo';
    const METHOD = 'method';

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setTooltipDescription($tooltipDescription)
    {
        return $this->setData(self::TOOLTIP_DESCRIPTION, $tooltipDescription);
    }

    /**
     * {@inheritdoc}
     */
    public function getTooltipDescription()
    {
        return $this->_get(self::TOOLTIP_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setLogo($logo)
    {
        return $this->setData(self::LOGO, $logo);
    }

    /**
     * {@inheritdoc}
     */
    public function getLogo()
    {
        return $this->_get(self::LOGO);
    }

    /**
     * {@inheritdoc}
     */
    public function setMethod($method)
    {
        return $this->setData(self::METHOD, $method);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->_get(self::METHOD);
    }
}

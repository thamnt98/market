<?php

namespace SM\Checkout\Model\Api\CheckoutWeb\ItemsValidMethod;

class Method extends \Magento\Framework\Api\AbstractExtensibleObject implements \SM\Checkout\Api\Data\CheckoutWeb\ItemsValidMethod\MethodInterface
{
    const METHOD_CODE = 'method_code';

    /**
     * {@inheritdoc}
     */
    public function setMethodCode($methodCode)
    {
        return $this->setData(self::METHOD_CODE, $methodCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodCode()
    {
        return $this->_get(self::METHOD_CODE);
    }
}

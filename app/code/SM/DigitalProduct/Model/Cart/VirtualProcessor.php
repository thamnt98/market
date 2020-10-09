<?php
/**
 * Class VirtualProcessor
 * @package SM\DigitalProduct\Model\Cart
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Son Nguyen <sonnn@smartosc.com>
 *
 * Copyright © 2020 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace SM\DigitalProduct\Model\Cart;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\CartItemProcessorInterface;

class VirtualProcessor implements CartItemProcessorInterface
{
    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $objectFactory;

    /**
     * @var \Magento\Quote\Api\Data\ProductOptionInterfaceFactory
     */
    private $productOptionFactory;

    /**
     * @var \Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory
     */
    private $productOptionExtensionFactory;

    /**
     * @var \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory
     */
    private $digitalDataFactory;

    /**
     * @var \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory
     */
    private $digitalTransactionDataFactory;

    /**
     * VirtualProcessor constructor.
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Quote\Api\Data\ProductOptionInterfaceFactory $productOptionFactory
     * @param \Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory
     * @param \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory $digitalDataFactory
     * @param \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory $digitalTransactionDataFactory
     */
    public function __construct(
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Quote\Api\Data\ProductOptionInterfaceFactory $productOptionFactory,
        \Magento\Quote\Api\Data\ProductOptionExtensionInterfaceFactory $productOptionExtensionFactory,
        \SM\DigitalProduct\Api\Data\DigitalInterfaceFactory $digitalDataFactory,
        \SM\DigitalProduct\Api\Data\DigitalTransactionInterfaceFactory $digitalTransactionDataFactory
    ) {
        $this->objectFactory = $objectFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionExtensionFactory = $productOptionExtensionFactory;
        $this->digitalDataFactory = $digitalDataFactory;
        $this->digitalTransactionDataFactory = $digitalTransactionDataFactory;
    }

    /**
     * @inheritDoc
     */
    public function convertToBuyRequest(CartItemInterface $cartItem)
    {
        $productOptions = $cartItem->getProductOption();
        if ($productOptions
            && $productOptions->getExtensionAttributes()
            && $productOptions->getExtensionAttributes()->getDigital()
        ) {
            $options['digital'] = $productOptions->getExtensionAttributes()->getDigital()->getData();
            $options['service_type'] = $productOptions->getExtensionAttributes()->getServiceType();
            $options['digital_transaction'] = $productOptions->getExtensionAttributes()->getDigitalTransaction()->getData();

            if (is_array($options)) {
                return $this->objectFactory->create($options);
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function processOptions(CartItemInterface $cartItem)
    {
        if ($cartItem->getProductType() !== \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL) {
            return $cartItem;
        }

        $buyRequest = $cartItem->getBuyRequest()->toArray();
        $productOptionExtension = $this->productOptionExtensionFactory->create();

        $this->setDigitalData($buyRequest, $productOptionExtension);

        $productOption = $this->productOptionFactory
            ->create()
            ->setExtensionAttributes($productOptionExtension);

        return $cartItem->setProductOption($productOption);
    }

    /**
     * @param $buyRequest
     * @param $productOptionExtension
     */
    protected function setDigitalData($buyRequest, $productOptionExtension)
    {
        if (isset($buyRequest['digital'])) {
            $digitalData = $this->digitalDataFactory->create();
            $digitalData->setData($buyRequest['digital']);
            $productOptionExtension->setDigital($digitalData);
        }

        if (isset($buyRequest['service_type'])) {
            $productOptionExtension->setServiceType($buyRequest['service_type']);
        }

        if (isset($buyRequest['digital_transaction'])) {
            $digitalData = $this->digitalTransactionDataFactory
                ->create()
                ->setData($buyRequest['digital_transaction']);
            $productOptionExtension->setDigitalTransaction($digitalData);
        }
    }
}

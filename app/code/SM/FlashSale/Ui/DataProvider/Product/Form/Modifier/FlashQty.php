<?php

namespace SM\FlashSale\Ui\DataProvider\Product\Form\Modifier;

use Magento\Framework\Stdlib\ArrayManager;

class FlashQty extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    /**
     * @var ArrayManager
     * @since 101.0.0
     */
    protected $arrayManager;

    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    public function modifyMeta(array $meta)
    {
        $meta = $this->customizeFieldSub($meta);

        return $meta;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    protected function customizeFieldSub(array $meta)
    {
        $isFlash = $this->arrayManager->findPath('is_flashsale', $meta, null, 'children');

        if ($isFlash) {
            $meta = $this->arrayManager->merge(
                $isFlash . static::META_CONFIG_PATH,
                $meta,
                [
                    'dataScope' => 'is_flashsale',
                    'options' => [
                        [
                            'value' => 0, 'label' => 'No'
                        ],
                        [
                            'value' => 1, 'label' => 'Yes'
                        ],
                    ]
                ]
            );
        }

        $weightPath = $this->arrayManager->findPath('flashsale_qty', $meta, null, 'children');

        if ($weightPath) {
            $meta = $this->arrayManager->merge(
                $weightPath . static::META_CONFIG_PATH,
                $meta,
                [
                    'dataScope' => 'flashsale_qty',
                    'validation' => [
                        'required-entry' => true,
                        'validate-greater-than-zero' => true
                    ],
                    'additionalClasses' => 'admin__field-small',
                    'imports' => [
                        'disabled' => '!${$.provider}:data.product.is_flashsale:value'
                    ]
                ]
            );
        }

        $flashCustomerQty = $this->arrayManager->findPath('flashsale_qty_per_customer', $meta, null, 'children');

        if ($flashCustomerQty) {
            $meta = $this->arrayManager->merge(
                $flashCustomerQty . static::META_CONFIG_PATH,
                $meta,
                [
                    'dataScope' => 'flashsale_qty_per_customer',
                    'validation' => [
                        'required-entry' => true,
                        'validate-greater-than-zero' => true
                    ],
                    'additionalClasses' => 'admin__field-small',
                    'imports' => [
                        'disabled' => '!${$.provider}:data.product.is_flashsale:value'
                    ]
                ]
            );
        }

        return $meta;
    }
}
<?php
namespace SM\MyVoucher\Model\Source\MobileRedirect;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * @api
 * @since 100.1.0
 */
class RedirectOption implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     * @since 100.1.0
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Campaign')
            ],
            [
                'value' => 2,
                'label' => __('Category')
            ],
            [
                'value' => 3,
                'label' => __('ProductPage')
            ],
            [
                'value' => 4,
                'label' => __('Homepage')
            ]
        ];
    }
}

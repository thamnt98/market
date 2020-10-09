<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 10:18 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Helper\Data;

class FilterList
{
    const CATEGORY_OPTION_CODE = 'cat';
    const RATING_OPTION_CODE   = 'rating';
    const DISCOUNT_OPTION_CODE = 'am_on_sale';
    const STOCK_OPTION_CODE    = 'stock';
    const NEW_OPTION_CODE      = 'am_is_new';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var int
     */
    protected $storeId;

    /**
     * ProductAttributeFilter constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                                  $request
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeId = (int)$request->getParam('store_id');
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function getFilterableAttributes()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributes */
        $productAttributes = $this->collectionFactory->create();
        $productAttributes->addFieldToFilter('is_filterable', 1);

        return $productAttributes;
    }

    /**
     * @param bool $onlyAttribute
     *
     * @return array|null
     */
    public function getAllOptions($onlyAttribute = false)
    {
        if ($onlyAttribute) {
            $options = [];
        } else {
            $options = $this->getAdditionalOptions();
        }

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        foreach ($this->getFilterableAttributes() as $attribute) {
            $options[$attribute->getAttributeCode()] = [
                'frontend_label' => $attribute->getStoreLabel($this->storeId),
                'attribute_code' => $attribute->getAttributeCode()
            ];
        }

        return $options;
    }

    /**
     * @return array[]
     */
    public function getAdditionalOptions()
    {
        return [
            self::CATEGORY_OPTION_CODE => [
                'frontend_label' => __('Category'),
                'attribute_code' => self::CATEGORY_OPTION_CODE
            ],
            self::RATING_OPTION_CODE   => [
                'frontend_label' => __('Rating'),
                'attribute_code' => self::RATING_OPTION_CODE
            ],
            self::DISCOUNT_OPTION_CODE => [
                'frontend_label' => __('Discount & Offer'),
                'attribute_code' => self::DISCOUNT_OPTION_CODE
            ],
            self::STOCK_OPTION_CODE    => [
                'frontend_label' => __('Stock'),
                'attribute_code' => self::STOCK_OPTION_CODE
            ],
            self::NEW_OPTION_CODE      => [
                'frontend_label' => __('Is New'),
                'attribute_code' => self::NEW_OPTION_CODE
            ]
        ];
    }
}

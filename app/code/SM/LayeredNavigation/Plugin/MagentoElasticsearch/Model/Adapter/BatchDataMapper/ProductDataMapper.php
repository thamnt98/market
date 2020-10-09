<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: May, 20 2020
 * Time: 9:32 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Plugin\MagentoElasticsearch\Model\Adapter\BatchDataMapper;

use SM\CustomPrice\Model\Customer as CustomerPrice;

class ProductDataMapper
{
    const DISCOUNT_PERCENT_FIELD_NAME = 'discount_percent';
    const DISCOUNT_PERCENT_PREFIX     = 'discount_percent_';

    /**
     * @var \Magento\GroupedProduct\Model\ResourceModel\Product\Link
     */
    protected $groupLink;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurableLink;

    /**
     * @var \Magento\Bundle\Model\Product\Type
     */
    protected $bundleLink;

    /**
     * @var int
     */
    protected $priceAttrId;

    /**
     * @var int
     */
    protected $specialAttrId;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var array
     */
    protected $result;

    /**
     * @var array
     */
    protected $attributeIds = [];

    /**
     * @var array
     */
    protected $basePriceAttributes = [];

    /**
     * ProductDataMapper constructor.
     *
     * @param \Magento\Framework\App\ResourceConnection                                  $resource
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute                         $attribute
     * @param \Magento\Bundle\Model\Product\Type                                         $bundleLink
     * @param \Magento\GroupedProduct\Model\ResourceModel\Product\Link                   $groupLink
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableLink
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        \Magento\Bundle\Model\Product\Type $bundleLink,
        \Magento\GroupedProduct\Model\ResourceModel\Product\Link $groupLink,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableLink
    ) {
        $this->connection = $resource->getConnection();
        $this->groupLink = $groupLink;
        $this->configurableLink = $configurableLink;
        $this->bundleLink = $bundleLink;
        $this->priceAttrId = $attribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, 'price');
        $this->specialAttrId = $attribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, 'special_price');
        $this->construct();
    }

    public function construct()
    {
        $select = $this->connection->select();
        $select->from('eav_attribute', 'attribute_code')
            ->where('attribute_code like ?', '%' . CustomerPrice::PREFIX_OMNI_NORMAL_PRICE . '%');
        $this->basePriceAttributes = $this->connection->fetchCol($select);
    }

    /**
     * @param \Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper $subject
     * @param array                                                                  $result
     *
     * @return array
     */
    public function afterMap(
        $subject,
        $result
    ) {
        $this->result = $result;
        foreach ($this->result as $productId => $data) {
            $this->convertData($data, $productId);
        }

        return $this->result;
    }

    protected function convertData($document, $productId)
    {
        $type = $this->getProductType($productId);
        foreach ($this->basePriceAttributes as $attrCode) {
            $this->updateBasePrice($attrCode, $productId, $document, $type);
            $locationCode = str_replace(CustomerPrice::PREFIX_OMNI_NORMAL_PRICE, '', $attrCode);
            $code = self::DISCOUNT_PERCENT_PREFIX . $locationCode;
            $document[$code] = $this->prepareDiscountValue(
                (float)$document[$attrCode],
                (float)$document[CustomerPrice::PREFIX_OMNI_FINAL_PRICE . $locationCode] ?? 0
            );
        }

        // Discount percent by magento price
//        $magentoPrice = (float)$this->getPrice($productId);
//        $magentoSpecial = (float)$this->getPrice($productId, true);
//        if ($magentoPrice > $magentoSpecial) {
//            $document[self::DISCOUNT_PERCENT_FIELD_NAME] = $this->prepareDiscountValue(
//                $magentoSpecial,
//                $magentoSpecial
//            );
//        } else {
//            $document[self::DISCOUNT_PERCENT_FIELD_NAME] = 0;
//        }

        $this->result[$productId] = $document;
    }

    /**
     * @param string $code
     * @param int|string $productId
     * @param array $document
     * @param string $type
     */
    protected function updateBasePrice($code, $productId, &$document, $type)
    {
        if (strpos($code, CustomerPrice::PREFIX_OMNI_NORMAL_PRICE) !== 0) {
            return;
        }

        $specialCode = str_replace(
            CustomerPrice::PREFIX_OMNI_NORMAL_PRICE,
            CustomerPrice::PREFIX_OMNI_FINAL_PRICE,
            $code
        );

        if ($type === 'simple') {
            $document[$code] = $document[$code] ?? 0;
            $document[$specialCode] = $document[$specialCode] ?? 0;
        } else {
            $childrenIds = $this->getChildrenIds($productId);
            if ($type === \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                $minPrice = ['price' => 0, 'special' => 0];
                foreach ($childrenIds as $optionId => $optionProductIds) {
                    $minOptionPrice = $this->getMinPrice($optionProductIds, $code, $specialCode);
                    $qty = $this->getBundleOptionQty($productId, $optionId, $minOptionPrice['productId'] ?? 0);
                    $minPrice['price'] += ($minOptionPrice['price'] ?? 0) * $qty;
                    $minPrice['special'] += ($minOptionPrice['special'] ?? 0) * $qty;
                }
            } else {
                $minPrice = $this->getMinPrice($childrenIds, $code, $specialCode);
            }

            $document[$code] = $minPrice['price'] ?? 0;
            $document[$specialCode] = $minPrice['special'] ?? 0;
        }

        $locationCode = str_replace(CustomerPrice::PREFIX_OMNI_NORMAL_PRICE, '', $code);
        $saleOffCode = \SM\LayeredNavigation\Helper\Data\FilterList::DISCOUNT_OPTION_CODE . '_' . $locationCode;
        if ($document[$specialCode] == 0 || $document[$specialCode] > $document[$code]) {
            $document[$specialCode] = $document[$code];
            $document[$saleOffCode] = 0;
        } else {
            $document[$saleOffCode] = $document[$specialCode] < $document[$code] ? 1 : 0;
        }
    }

    /**
     * @param array $productIds
     * @param       $baseCode
     * @param       $promoCode
     *
     * @return array
     */
    protected function getMinPrice($productIds, $baseCode, $promoCode)
    {
        $realPrice = 0;
        $realSpecial = 0;
        $minProductId = 0;
        foreach ($productIds as $childrenId) {
            if (!$this->isSalable($childrenId)) {
                continue;
            }

            $childrenType = $this->getProductType($childrenId);
            if ($childrenType === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $configPrice = $this->getMinPrice(
                    $this->getChildrenIds($childrenId),
                    $baseCode,
                    $promoCode
                );

                $price = $configPrice['price'] ?? 0;
                $special = $configPrice['special'] ?? 0;
            } else {
                if (isset($this->result[$childrenId][$baseCode])) {
                    $price = $this->result[$childrenId][$baseCode] ?? 0;
                } else {
                    $price = $this->getRawBasePrice($childrenId, $baseCode);
                }

                if (isset($this->result[$childrenId][$promoCode])) {
                    $special = $this->result[$childrenId][$promoCode] ?? 0;
                } else {
                    $special = $this->getRawBasePrice($childrenId, $promoCode);
                }
            }

            if ($childrenType === \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                if (((float) $price) <= 0) {
                    $price = $this->getPrice($childrenId);
                }
                if (((float) $special) <= 0) {
                    $special = $this->getPrice($childrenId, true);
                }
            }

            $price = (float) $price;
            $special = (float) $special;
            if (!$special || $special > $price) {
                $special = $price;
            }

            if ($realSpecial && $special && $realSpecial > $special) {
                $realPrice = $price;
                $realSpecial = $special;
                $minProductId = $childrenId;
                continue;
            }

            if ($price !== 0 && ($realPrice === 0 || $realPrice > $price)) {
                $realPrice = $price;
                $realSpecial = $special;
                $minProductId = $childrenId;
            }
        }

        return ['price' => $realPrice, 'special' => $realSpecial, 'productId' => $minProductId];
    }

    /**
     * @param $basePrice
     * @param $promoPrice
     *
     * @return int|int[]
     */
    protected function prepareDiscountValue($basePrice, $promoPrice)
    {
        if (is_array($basePrice)) {
            $result = [];
            foreach ($basePrice as $key => $item) {
                $result[$key] = $this->prepareDiscountValue($item, $promoPrice[$key] ?? null);
            }
        } else {
            $result = null;
            $basePrice = (float) $basePrice;
            $promoPrice = (float) $promoPrice;
            if (!empty($promoPrice) && !empty($basePrice) && $basePrice > $promoPrice) {
                $result = ceil(($basePrice - $promoPrice) / $basePrice * 10) * 10;
            }
        }

        return $result;
    }

    /**
     * @param int|string $productId
     * @param bool       $isSpecial
     *
     * @return float
     */
    protected function getPrice($productId, $isSpecial = false)
    {
        $attrId = $isSpecial ? $this->specialAttrId : $this->priceAttrId;
        $select = $this->connection->select();
        $select->from(['decimal' => 'catalog_product_entity_decimal'], 'value')
            ->joinInner(['entity' => 'catalog_product_entity'], 'decimal.row_id = entity.row_id', [])
            ->where('entity.entity_id = ?', $productId)
            ->where('decimal.attribute_id = ?', $attrId)
            ->limit(1);

        return (float)$this->connection->fetchOne($select);
    }

    /**
     * @param int|string $productId
     *
     * @return string
     */
    protected function getProductType($productId)
    {
        $select = $this->connection->select();
        $select->from('catalog_product_entity', 'type_id')
            ->where('entity_id = ?', $productId)
            ->limit(1);

        return $this->connection->fetchOne($select);
    }

    /**
     * @param int|string $id
     *
     * @return array
     */
    protected function getChildrenIds($id)
    {
        $type = $this->getProductType($id);
        switch ($type) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                return $this->configurableLink->getChildrenIds($id)[0] ?? [];
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                return $this->groupLink->getChildrenIds(
                    $id,
                    \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED
                )[\Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED] ?? [];
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->bundleLink->getChildrenIds($id);
            default:
                return [$id];
        }
    }

    /**
     * @param int|string $productId
     * @param string     $attrCode
     *
     * @return float
     */
    public function getRawBasePrice($productId, $attrCode)
    {
        $attrId = $this->getBasePriceId($attrCode);
        $select = $this->connection->select();
        $select->from(['decimal' => 'catalog_product_entity_decimal'], 'value')
            ->joinInner(['entity' => 'catalog_product_entity'], 'decimal.row_id = entity.row_id', [])
            ->where('entity.entity_id = ?', $productId)
            ->where('decimal.attribute_id = ?', $attrId)
            ->limit(1);

        return (float)$this->connection->fetchOne($select);
    }

    /**
     * @param string $attrCode
     *
     * @return int
     */
    protected function getBasePriceId($attrCode)
    {
        if (!isset($this->attributeIds[$attrCode])) {
            $select = $this->connection->select();
            $select->from('eav_attribute', 'attribute_id')
                ->where('attribute_code = ?', $attrCode)
                ->limit(1);
            $this->attributeIds[$attrCode] = (int)$this->connection->fetchOne($select);
        }

        return $this->attributeIds[$attrCode];
    }

    /**
     * @param $productId
     *
     * @return bool
     */
    protected function isSalable($productId)
    {
        $select = $this->connection->select();
        $select->from(['i' => 'inventory_source_item'], 'count(i.source_item_id)')
            ->joinInner(['p' => 'catalog_product_entity'], 'p.sku = i.sku')
            ->where('p.entity_id = ?', $productId)
            ->where('i.status = ?', 1);

        return (bool)$this->connection->fetchOne($select);
    }

    /**
     * @param $bundleId
     * @param $optionId
     * @param $productId
     *
     * @return int
     */
    protected function getBundleOptionQty($bundleId, $optionId, $productId)
    {
        $select = $this->connection->select();
        $select->from(['catalog_product_bundle_selection'], 'selection_qty')
            ->where('option_id = ?', $optionId)
            ->where('product_id = ?', $productId)
            ->where('parent_product_id = ?', $bundleId)
            ->limit(1);

        return (int) $this->connection->fetchOne($select);
    }
}

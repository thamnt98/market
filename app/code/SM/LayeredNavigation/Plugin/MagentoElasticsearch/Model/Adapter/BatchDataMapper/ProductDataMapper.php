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

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use SM\CustomPrice\Model\Customer as CustomerPrice;

class ProductDataMapper
{
    const DISCOUNT_PERCENT_FIELD_NAME = 'discount_percent';
    const DISCOUNT_PERCENT_PREFIX     = 'discount_percent_';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider
     */
    protected $dataProvider;

    /**
     * @var int
     */
    protected $priceAttrId;

    /**
     * @var int
     */
    protected $specialAttrId;

    /**
     * @var int
     */
    protected $statusAttrId;

    /**
     * @var array
     */
    protected $result;

    /**
     * @var array
     */
    protected $basePriceAttributes = [];

    /**
     * @var array
     */
    protected $dynamicPriceAttributes = [];

    /**
     * @var array
     */
    protected $allProductId = [];

    /**
     * @var array
     */
    protected $parentProduct = [];

    /**
     * @var array
     */
    protected $productRawData = [];

    /**
     * @var array
     */
    protected $bundleOptions = [];

    /**
     * ProductDataMapper constructor.
     *
     * @param \Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider $dataProvider
     * @param \Magento\Framework\App\ResourceConnection                         $resource
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider $dataProvider,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->connection = $resource->getConnection();
        $this->dataProvider = $dataProvider;
        $this->construct();
    }

    protected function construct()
    {
        if (empty($this->dynamicPriceAttributes)) {
            $select = $this->connection->select();
            $select->from(['a' => 'eav_attribute'], ['attribute_code', 'attribute_id'])
                ->join(
                    ['t' => 'eav_entity_type'],
                    'a.entity_type_id = t.entity_type_id',
                    []
                )->where(
                    't.entity_type_code = ?',
                    \Magento\Catalog\Model\Product::ENTITY
                )->where(
                    "attribute_code LIKE '%" . CustomerPrice::PREFIX_OMNI_NORMAL_PRICE . "%' "
                    . "OR attribute_code LIKE '%" . CustomerPrice::PREFIX_OMNI_FINAL_PRICE . "%' "
                    . "OR attribute_code IN ('price','status','special_price')"
                );
            $data = $this->connection->fetchAll($select);
            $this->dynamicPriceAttributes = array_combine(
                array_column($data, 'attribute_code'),
                array_column($data, 'attribute_id')
            );

            if (isset($this->dynamicPriceAttributes['price'])) {
                $this->priceAttrId = $this->dynamicPriceAttributes['price'];
                unset($this->dynamicPriceAttributes['price']);
            }

            if (isset($this->dynamicPriceAttributes['special_price'])) {
                $this->specialAttrId = $this->dynamicPriceAttributes['special_price'];
                unset($this->dynamicPriceAttributes['special_price']);
            }

            if (isset($this->dynamicPriceAttributes['status'])) {
                $this->statusAttrId = $this->dynamicPriceAttributes['status'];
                unset($this->dynamicPriceAttributes['status']);
            }
        }
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
        $this->prepareProductData(array_keys($result));

        foreach ($this->result as $productId => $data) {
            $this->convertData($productId);
        }

        return $this->result;
    }

    /**
     * @param int   $productId
     */
    protected function convertData($productId)
    {
        if (empty($this->productRawData[$productId]['type_id'])) {
            return;
        }

        $type = $this->productRawData[$productId]['type_id'];
        foreach ($this->basePriceAttributes as $attrCode) {
            $locationCode = (int)str_replace(CustomerPrice::PREFIX_OMNI_NORMAL_PRICE, '', $attrCode);

            if (!$locationCode) {
                continue;
            }

            switch ($type) {
                case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                    $this->updateConfigGroup($productId, $locationCode);
                    break;
                case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                    $this->updateBundlePrice($productId, $locationCode);
                    break;
                default:
                    $this->updateSimplePrice($productId, $locationCode);
            }

            $this->updateOnSale($productId, $locationCode);
            $this->updateDiscountPercent($productId, $locationCode);
        }
    }

    /**
     * Update base price for single product.
     *
     * @param int|string $productId
     * @param string     $code
     */
    protected function updateSimplePrice($productId, $code)
    {
        if (empty($this->result[$productId])) {
            return;
        }

        $baseAttr = CustomerPrice::PREFIX_OMNI_NORMAL_PRICE . $code;
        $specialAttr = CustomerPrice::PREFIX_OMNI_FINAL_PRICE . $code;
        $this->result[$productId][$baseAttr] = $this->getPrice($productId, $baseAttr);
        $this->result[$productId][$specialAttr] = $this->getPrice($productId, $specialAttr, true);
    }

    /**
     * Update base price for configurable, group product.
     *
     * @param int|string $productId
     * @param string     $code
     */
    protected function updateConfigGroup($productId, $code)
    {
        if (empty($this->result[$productId]) || empty($this->parentProduct[$productId])) {
            return;
        }

        $baseAttr = CustomerPrice::PREFIX_OMNI_NORMAL_PRICE . $code;
        $specialAttr = CustomerPrice::PREFIX_OMNI_FINAL_PRICE . $code;
        $minPrice = $this->getMinChildrenPrice($this->parentProduct[$productId], $baseAttr, $specialAttr);
        $this->result[$productId][$baseAttr] = $minPrice['price'] ?? 0;
        $this->result[$productId][$specialAttr] = $minPrice['special'] ?? 0;
    }

    /**
     * Update base price for bundle product.
     *
     * @param int|string $productId
     * @param string     $code
     */
    protected function updateBundlePrice($productId, $code)
    {
        if (empty($this->result[$productId]) || empty($this->bundleOptions[$productId])) {
            return;
        }

        $minPrice = ['price' => 0, 'special' => 0];
        $baseAttr = CustomerPrice::PREFIX_OMNI_NORMAL_PRICE . $code;
        $specialAttr = CustomerPrice::PREFIX_OMNI_FINAL_PRICE . $code;

        foreach ($this->bundleOptions[$productId] as $optionId => $optionProduct) {
            $minOption = $this->getMinChildrenPrice(array_keys($optionProduct), $code);
            $qty = $optionProduct[$minOption['productId']] ?? 1;
            $minPrice['price'] += ($minOption['price'] ?? 0) * $qty;
            $minPrice['special'] += ($minOption['special'] ?? 0) * $qty;
        }

        $this->result[$productId][$baseAttr] = $minPrice['price'] ?? 0;
        $this->result[$productId][$specialAttr] = $minPrice['special'] ?? 0;
    }

    /**
     * Update on sale product by location code.
     *
     * @param int|string $productId
     * @param string     $code
     */
    protected function updateOnSale($productId, $code)
    {
        if (empty($this->result[$productId])) {
            return;
        }

        $baseAttr = CustomerPrice::PREFIX_OMNI_NORMAL_PRICE . $code;
        $specialAttr = CustomerPrice::PREFIX_OMNI_FINAL_PRICE . $code;
        $saleOffCode = \SM\LayeredNavigation\Helper\Data\FilterList::DISCOUNT_OPTION_CODE . '_' . $code;
        $data = $this->result[$productId];
        if ($data[$specialAttr] == 0 || $data[$specialAttr] > $data[$baseAttr]) {
            $this->result[$productId][$specialAttr] = $data[$baseAttr];
            $this->result[$productId][$saleOffCode] = 0;
        } else {
            $this->result[$productId][$saleOffCode] = $data[$specialAttr] < $data[$baseAttr] ? 1 : 0;
        }
    }

    /**
     * Update discount percent product by location code.
     *
     * @param int|string $productId
     * @param string     $code
     */
    protected function updateDiscountPercent($productId, $code)
    {
        if (empty($this->result[$productId])) {
            return;
        }

        $discountCode = self::DISCOUNT_PERCENT_PREFIX . $code;
        $baseAttr = CustomerPrice::PREFIX_OMNI_NORMAL_PRICE . $code;
        $specialAttr = CustomerPrice::PREFIX_OMNI_FINAL_PRICE . $code;
        $this->result[$productId][$discountCode] = $this->prepareDiscountValue(
            (float)$this->result[$productId][$baseAttr],
            (float)$this->result[$productId][$specialAttr] ?? 0
        );
    }

    /**
     * @param array    $productIds
     * @param string   $code
     * @param null|int $returnId
     *
     * @return array
     */
    protected function getMinChildrenPrice($productIds, $code, $returnId = null)
    {
        $realPrice = $realSpecial = $minProductId = 0;
        $baseAttr = CustomerPrice::PREFIX_OMNI_NORMAL_PRICE . $code;
        $specialAttr = CustomerPrice::PREFIX_OMNI_FINAL_PRICE . $code;
        foreach ($productIds as $childrenId) {
            if (!$this->isSalable($childrenId)) {
                continue;
            }

            if (!empty($this->parentProduct[$childrenId])) {
                $min = $this->getMinChildrenPrice(
                    $this->parentProduct[$childrenId],
                    $code,
                    $returnId ?: $childrenId
                );

                $price = (float)($min['price'] ?? 0);
                $special = (float)($min['special'] ?? 0);
            } else {
                $price = $this->getPrice($childrenId, $baseAttr);
                $special = $this->getPrice($childrenId, $specialAttr, true);
            }

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

        return ['price' => $realPrice, 'special' => $realSpecial, 'productId' => $returnId ?: $minProductId];
    }

    /**
     * @param int    $productId
     * @param string $priceCode
     * @param false  $isSpecial
     *
     * @return float
     */
    protected function getPrice($productId, $priceCode, $isSpecial = false)
    {
        $price = (float)($this->productRawData[$productId][$priceCode] ?? 0);

        if (!$price) {
            $rawCode = $isSpecial ? 'special_price' : 'price';
            $price = (float)($this->productRawData[$productId][$rawCode] ?? 0);
        }

        return $price;
    }

    /**
     * @param $basePrice
     * @param $promoPrice
     *
     * @return int|int[]
     */
    protected function prepareDiscountValue($basePrice, $promoPrice)
    {
        $result = null;
        $basePrice = (float)$basePrice;
        $promoPrice = (float)$promoPrice;
        if (!empty($promoPrice) && !empty($basePrice) && $basePrice > $promoPrice) {
            $result = ceil(($basePrice - $promoPrice) / $basePrice * 10) * 10;
        }

        return $result;
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
     * @param string     $type
     *
     * @return array
     */
    protected function getChildrenIds($id, $type)
    {
        $hasChildrenCode = [
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            \Magento\Bundle\Model\Product\Type::TYPE_CODE,
        ];
        if (!in_array($type, $hasChildrenCode)) {
            return null;
        }

        return $this->dataProvider->getProductChildIds($id, $type);
    }

    /**
     * @param $productId
     *
     * @return bool
     */
    protected function isSalable($productId)
    {
        return isset($this->productRawData[$productId]['status'])
            && $this->productRawData[$productId]['status'] == ProductStatus::STATUS_ENABLED
            && (
                !empty($this->productRawData[$productId]['source']) ||
                !empty($this->parentProduct[$productId])
            );
    }

    /**
     * Generate product data.
     *
     * @param $productIds
     */
    protected function prepareProductData($productIds)
    {
        $this->prepareChildren($productIds);
        $select = $this->connection->select();
        $select->from(
            ['p' => 'catalog_product_entity'],
            ['entity_id', 'type_id']
        )->joinLeft(
            ['i' => 'inventory_source_item'],
            'p.sku = i.sku and i.status = 1',
            'count(i.source_item_id) as source'
        )->where(
            'entity_id IN (?)',
            $this->allProductId
        )->group('p.entity_id');

        $this->generatePriceQuery($select);
        $this->productRawData = $this->connection->fetchAssoc($select);
        $this->prepareBundleOptions();
    }

    /**
     * Generate children product.
     *
     * @param $productIds
     */
    protected function prepareChildren($productIds)
    {
        $data = $this->getProductTypes($productIds);
        foreach ($data as $productData) {
            $this->allProductId[] = $productData['entity_id'];
            if (empty($this->parentProduct[$productData['entity_id']])) {
                if ($children = $this->getChildrenIds($productData['entity_id'], $productData['type_id'])) {
                    $this->parentProduct[$productData['entity_id']] = $children;
                    $this->prepareChildren($children);
                }
            }
        }
    }

    /**
     * Get product types by product ids.
     *
     * @param $productIds
     *
     * @return array
     */
    protected function getProductTypes($productIds)
    {
        if (empty($productIds) || !is_array($productIds)) {
            return [];
        }

        $select = $this->connection->select();
        $select->from(
            ['p' => 'catalog_product_entity'],
            ['entity_id', 'type_id']
        )->where(
            'entity_id IN (?)',
            $productIds
        )->group('p.entity_id');

        return $this->connection->fetchAssoc($select);
    }

    /**
     * Generate bundle options
     */
    protected function prepareBundleOptions()
    {
        $bundleIds = [];
        foreach ($this->productRawData as $id => $data) {
            if (isset($data['type_id']) && $data['type_id'] === \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                $bundleIds[] = $id;
            }
        }

        if (empty($bundleIds)) {
            return;
        }

        $select = $this->connection->select();
        $select->from(
            ['option' => 'catalog_product_bundle_selection'],
            ['option_id', 'product_id', 'selection_qty']
        )->joinInner(
            ['p' => 'catalog_product_entity'],
            'p.row_id = option.parent_product_id',
            ['entity_id']
        )->where('p.entity_id IN (?)', $bundleIds);

        $data = $this->connection->fetchAll($select);
        foreach ($data as $item) {
            $this->bundleOptions[$item['entity_id']]
            [$item['option_id']]
            [$item['product_id']] = $item['selection_qty'];
        }
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     */
    protected function generatePriceQuery($select)
    {
        $stores = [0];
        $priceAttrs = $this->dynamicPriceAttributes;
        $priceAttrs['price'] = $this->priceAttrId;
        $priceAttrs['special_price'] = $this->specialAttrId;

        foreach ($this->result as $item) {
            if (isset($item['store_id'])) {
                $stores[] = (int)$item['store_id'];
                break;
            }
        }

        $defaultSelect = $this->connection->select()
            ->from(
                ['decimal' => 'catalog_product_entity_decimal'],
                'value'
            )->where(
                'row_id = p.row_id'
            )->where(
                'store_id IN (?)',
                $stores
            )->order(
                'store_id DESC'
            )->limit(1);

        foreach ($priceAttrs as $attrCode => $attrId) {
            $attrSelect = clone $defaultSelect;
            $attrSelect->where('attribute_id = ?', $attrId);
            $select->columns([
                '(' . $attrSelect->__toString() . ') AS ' . $attrCode,
            ]);

            // Add attribute to `basePriceAttributes` property.
            if (strpos($attrCode, CustomerPrice::PREFIX_OMNI_NORMAL_PRICE) === 0) {
                $this->basePriceAttributes[] = $attrCode;
            }
        }

        $statusSelect = $this->connection->select()
            ->from(
                ['int' => 'catalog_product_entity_int'],
                'value'
            )->where(
                'row_id = p.row_id'
            )->where(
                'attribute_id = ?',
                $this->statusAttrId
            )->where(
                'store_id IN (?)',
                $stores
            )->order(
                'store_id DESC'
            )->limit(1);
        $select->columns([
            '(' . $statusSelect->__toString() . ') AS status',
        ]);
    }
}

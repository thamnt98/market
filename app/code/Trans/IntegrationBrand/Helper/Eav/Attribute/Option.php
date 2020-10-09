<?php
/**
 * @category Trans
 * @package  Trans_IntegrationBrand
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Anan Fauzi <anan.fauzi@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\IntegrationBrand\Helper\Eav\Attribute;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class \Trans\IntegrationBrand\Helper\Eav\Attribute\Option
 */
class Option extends AbstractHelper
{
  /**
   * @var string
   */
  const BRAND_ATTRIBUTE_CODE = 'brand';

  /**
   * @var string
   */
  const SHOPBY_BRAND_ATTRIBUTE_CODE = 'shop_by_brand';

  /**
   * @var string
   */
  const MESSAGE_OPTION_ERROR = 'Failed on creating eav attribute option';

  /**
   * @var string
   */
  const MESSAGE_EMPTY_DATA = 'Option data is empty';

  /**
   * @var string
   */
  const INDEX_SHOPBY_BRAND_VALUE = 'brand_name';

  /**
   * @var string
   */
  const PIM_INDEX_VALUE = 'data_value';

  /**
   * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
   */
  protected $attributeFactory;

  /**
   * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
   */
  protected $optionCollection;

  /**
   * @var \Magento\Framework\Serialize\Serializer\Json
   */
  protected $json;

  /**
   * @var \Magento\Store\Model\StoreManagerInterface
   */
  protected $storeManager;

  /**
   * @var \Magento\Eav\Setup\EavSetupFactory
   */
  protected $eavSetupFactory;

  /**
   * Constructor method
   * @param Context               $context
   * @param Attribute             $attributeFactory
   * @param CollectionFactory     $optionCollection
   * @param Json                  $json
   * @param StoreManagerInterface $storeManager
   * @param EavSetupFactory       $eavSetupFactory
   */
  public function __construct(
    Context $context,
    Attribute $attributeFactory,
    CollectionFactory $optionCollection,
    Json $json,
    StoreManagerInterface $storeManager,
    EavSetupFactory $eavSetupFactory
  ) {
    $this->attributeFactory = $attributeFactory;
    $this->optionCollection = $optionCollection;
    $this->json = $json;
    $this->storeManager = $storeManager;
    $this->eavSetupFactory = $eavSetupFactory;
    parent::__construct($context);
  }

  /**
   * Init attribute collection
   * @param  string $attrCode [description]
   * @return \Magento\Catalog\Model\ResourceModel\Eav\Attribute
   */
  public function initAttribute(string $attrCode)
  {
    $attributeInfo = $this->attributeFactory->getCollection();
    $attributeInfo->addFieldToFilter('attribute_code',['eq' => $attrCode]);
    $data = $attributeInfo->getFirstItem();
    return $data;
  }

  /**
   * Get Attribute Option Value
   * @param  string $value
   * @param  string $attrId
   * @return \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
   */
  public function getOptionValue($value, $attrId, $store = 0)
  {
    $option = $this->optionCollection->create();
    $option->addFieldToFilter('tdv.value', $value);
    $option->addFieldToFilter('attribute_id', $attrId);
    ($store)? $option->setStoreFilter($store) : $option->setStoreFilter();
    $option->setPageSize(1);
    return $option;
  }
}

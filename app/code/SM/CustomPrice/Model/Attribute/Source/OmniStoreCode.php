<?php


namespace SM\CustomPrice\Model\Attribute\Source;


class OmniStoreCode extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory
     */
    private $collectionFactory;

    /**
     * OmniStoreCode constructor.
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $collection = $this->_createCollection();
            /** @var \Magento\Inventory\Model\Source $option */
            foreach ($collection as $option) {
                $this->_options[] = ['label' => $option->getName(), 'value' => $option->getSourceCode()];
            }

        }
        return $this->_options;
    }

    /**
     * @param string $value
     * @return array|string
     */
    public function getOptionText($value)
    {
        if (!$value) {
            $value = '0';
        }
        $isMultiple = false;
        if (strpos($value, ',') !== false) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        if (!$this->_options) {
            $collection = $this->_createCollection();

            $this->_options = $collection->load()->toOptionArray();
        }

        if ($isMultiple) {
            $values = [];
            foreach ($value as $val) {
                $values[] = $this->_options[$val];
            }
            return $values;
        } else {
            return $this->_options[$value];
        }
    }

    public function _createCollection()
    {
        return $this->collectionFactory->create();
    }
}
<?php


namespace SM\Customer\Model\ResourceModel\Address\Attribute\Source;


use Trans\LocationCoverage\Model\ResourceModel\City\Collection;
use Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory as CityFactory ;

class City extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{

    /**
     * @var CityFactory
     */
    protected $_cityFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param CityFactory $cityFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        CityFactory $cityFactory
    ) {
        $this->_cityFactory = $cityFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $cityCollection = $this->_createCityCollection()->load();
            $this->_options = [];
            foreach ($cityCollection as $city) {
                $this->_options[] = [
                    'label' => $city->getCity(),
                    'value' => $city->getId()
                ];
            }
        }
        return $this->_options;
    }

    /**
     * @return Collection
     */
    protected function _createCityCollection()
    {
        return $this->_cityFactory->create();
    }
}

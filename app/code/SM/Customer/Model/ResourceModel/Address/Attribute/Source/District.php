<?php


namespace SM\Customer\Model\ResourceModel\Address\Attribute\Source;


use Magento\Customer\Model\CustomerFactory;
use Trans\LocationCoverage\Model\ResourceModel\District\Collection;
use Trans\LocationCoverage\Model\ResourceModel\District\CollectionFactory as DistrictFactory;

class District extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{

    /**
     * @var DistrictFactory
     */
    protected $_districtFactory;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param DistrictFactory $districtFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        DistrictFactory $districtFactory,
        \Magento\Framework\App\Request\Http $request,
        CustomerFactory $customerFactory
    ) {
        $this->request = $request;
        $this->_districtFactory = $districtFactory;
        $this->customerFactory = $customerFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $customerId = $this->request->getParam('id');
        if (!empty($customerId)) {
            $customer = $this->customerFactory->create()->load($customerId);
            if (!empty($customer)) {
                $city = $customer->getCity();
            }
        }
        if (!$this->_options) {
            $districtCollection = $this->_createDistrictCollection();
            if (!empty($city)) {
                $districtCollection->addFieldToFilter('entity_id', $city);
            }
            $districtCollection->load();
            $this->_options = [];
            foreach ($districtCollection as $district) {
                $this->_options[] = [
                    'label' => $district->getDistrict(),
                    'value' => $district->getDistrictId(),
                    'city' => $district->getId(),
                    'city_id' => $district->getEntityId()
                ];
            }
        }
        return $this->_options;
    }

    public function getRuleConditionOptions()
    {
        $districtCollection = $this->_createDistrictCollection();
        $districtCollection->join('regency', 'main_table.entity_id = regency.entity_id', 'city');
        $city = [];

        foreach ($districtCollection as $district) {
            $city[$district->getEntityId()] = [
                'label' => $district->getCity(),
                'value' => []
            ];
        }

        foreach ($districtCollection as $district) {
            $city[$district->getEntityId()]['value'][] = [
                'label' => $district->getDistrict(),
                'value' => $district->getDistrictId()
            ];
        }
        $this->_options = $city;
        return $this->_options;
    }

    /**
     * @return Collection
     */
    protected function _createDistrictCollection()
    {
        return $this->_districtFactory->create();
    }
}

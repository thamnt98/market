<?php

namespace SM\ProvinceGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Trans\LocationCoverage\Api\Data\CityInterface;
use Trans\LocationCoverage\Api\Data\DistrictInterface;
use Trans\LocationCoverage\Model\ResourceModel\District\CollectionFactory;

/**
 * Class Province
 * @package SM\ProvinceGraphQl\Model\Resolver
 */
class Province implements ResolverInterface
{
    /**
     * @var \Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory
     */
    protected $cityCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $districtCollectionFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * Province constructor.
     * @param \Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory $cityCollectionFactory
     * @param CollectionFactory $districtCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     */
    public function __construct(
        \Trans\LocationCoverage\Model\ResourceModel\City\CollectionFactory $cityCollectionFactory,
        CollectionFactory $districtCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
    )
    {
        $this->cityCollectionFactory = $cityCollectionFactory;
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $cityCollection = $this->cityCollectionFactory->create()->addFieldToSelect('*')
            ->setOrder(CityInterface::CITY_NAME, 'ASC');
        /** @var Collection $districtCollection */
        $districtCollection = $this->districtCollectionFactory->create()
            ->setOrder(DistrictInterface::ENTITY_ID, 'ASC');

        $regionCollection = $this->regionCollectionFactory->create()->addFieldToFilter('country_id', 'ID');

        $districtOfCity = [];

        foreach ($districtCollection as $item) {
            $districtOfCity[$item->getEntityId()][$item->getDistrictId()]['id'] = (int)$item->getDistrictId();
            $districtOfCity[$item->getEntityId()][$item->getDistrictId()]['name'] = $item->getDistrict();
        }

        $cityOfRegion = [];
        foreach ($cityCollection as $item) {
            if (isset($districtOfCity[$item->getEntityId()])) {
                $cityOfRegion[$item->getRegionId()][$item->getEntityId()] = [
                    'id' => (int)$item->getEntityId(),
                    'name' => $item->getCity(),
                    'district' => array_values($districtOfCity[$item->getEntityId()])
                ];
            }
        }
        $regionList = [];
        foreach ($regionCollection as $item) {
            if (isset($cityOfRegion[$item->getCode()])) {
                $regionList[] = [
                    'id' => (int)$item->getId(),
                    'name' => $item->getName(),
                    'city' => array_values($cityOfRegion[$item->getCode()])
                ];
            }
        }
        return $regionList;
    }
}

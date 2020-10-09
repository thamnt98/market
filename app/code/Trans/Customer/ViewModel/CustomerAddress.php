<?php
/**
 * @category    SM
 * @package     SM_Customer
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author      Nam Nguyen <namnd2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace Trans\Customer\ViewModel;

/**
 * Class CustomerAddress
 * @package Trans\Customer\ViewModel
 */
class CustomerAddress implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Address\CollectionFactory
     */
    protected $addressCollectionFactory;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $postHelper;

    /**
     * @var \Trans\LocationCoverage\Model\CityFactory
     */
    protected $cityFactory;

    /**
     * @var \Trans\LocationCoverage\Model\DistrictFactory
     */
    protected $districtFactory;

    /**
     * CustomerAddress constructor.
     * @param \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressCollectionFactory
     * @param \Magento\Framework\Data\Helper\PostHelper $postHelper
     * @param \Trans\LocationCoverage\Model\CityFactory $cityFactory
     * @param \Trans\LocationCoverage\Model\DistrictFactory $districtFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Address\CollectionFactory $addressCollectionFactory,
        \Magento\Framework\Data\Helper\PostHelper $postHelper,
        \Trans\LocationCoverage\Model\CityFactory $cityFactory,
        \Trans\LocationCoverage\Model\DistrictFactory $districtFactory
    ) {
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->postHelper = $postHelper;
        $this->cityFactory = $cityFactory;
        $this->districtFactory = $districtFactory;
    }

    /**
     * @param int $id
     * @return string
     */
    public function getCityName($id)
    {
        $city = $this->cityFactory->create()->load($id);
        if ($city->getId()) {
            return $city->getCity();
        }
        return '';
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDistrictName($id)
    {
        $district = $this->districtFactory->create()->load($id);
        if ($district->getId()) {
            return $district->getDistrict();
        }
        return '';
    }

    /**
     * @param $customer
     * @return \Magento\Customer\Model\ResourceModel\Address\Collection
     */
    public function getAddressCollection($customer)
    {
        $collection = $this->addressCollectionFactory->create()->addFieldToSelect('*');
        $collection->setOrder('entity_id', 'desc');
        $collection->setCustomerFilter([$customer->getId()]);
        return $collection;
    }

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getStreet($address)
    {
        $street = $address->getStreet();
        return isset($street[0]) ? $street[0] : '';
    }

    /**
     * @param \Magento\Customer\Api\Data\AddressInterface $address
     * @return string
     */
    public function getAddressDetail($address)
    {
        $street = $address->getStreet();
        return isset($street[1]) ? $street[1] : '';
    }

    /**
     * @param string $url
     * @param array $params
     * @return string
     */
    public function getDeleteAddressParams($url, $params)
    {
        return $this->postHelper->getPostData(
            $url,
            $params
        );
    }
}

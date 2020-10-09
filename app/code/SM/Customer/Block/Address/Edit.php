<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: April, 25 2020
 * Time: 8:38 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Block\Address;

class Edit extends \Trans\Customer\Block\Address\Edit
{
    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $addressHelper;

    /**
     * Edit constructor.
     *
     * @param \Magento\Customer\Helper\Address                                 $addressHelper
     * @param \Magento\Framework\View\Element\Template\Context                 $context
     * @param \Magento\Directory\Helper\Data                                   $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface                         $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config                         $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory  $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Customer\Model\Session                                  $customerSession
     * @param \Magento\Customer\Api\AddressRepositoryInterface                 $addressRepository
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory               $addressDataFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer                 $currentCustomer
     * @param \Magento\Framework\Api\DataObjectHelper                          $dataObjectHelper
     * @param array                                                            $data
     * @param \Magento\Customer\Api\AddressMetadataInterface|null              $addressMetadata
     */
    public function __construct(
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        array $data = [],
        \Magento\Customer\Api\AddressMetadataInterface $addressMetadata = null
    ) {
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $customerSession,
            $addressRepository,
            $addressDataFactory,
            $currentCustomer,
            $dataObjectHelper,
            $data,
            $addressMetadata
        );

        $this->addressHelper = $addressHelper;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function prepareFormData()
    {
        $data = [
            'address_tag'   => '',
            'fullname'      => '',
            'telephone'     => '',
            'region_id'     => '',
            'city_name'     => '',
            'city'          => '',
            'postcode'      => '',
            'street'        => '',
            'district_id'   => '',
            'city_id'       => '',
            'district_name' => ''
        ];

        if (!$this->getCustomerAddressCount()) {
            /** @var \Trans\Customer\Block\Address\Grid $addressGrid */
            if ($addressGrid = $this->createBlock(\Trans\Customer\Block\Address\Grid::class)) {
                $customer = $this->getCustomer();
                $data['address_tag'] = __('Home');
                $data['fullname'] = $addressGrid->getFullName($customer);
                $data['telephone'] = $addressGrid->getTelephoneNumber($customer);
                $data['district_id'] = $addressGrid->getDistrictAddress($customer);
                $data['city_id'] = $addressGrid->getCityAddress($customer);
            }
        } else {
            $data['address_tag'] = $this->getAddressTag();
            $data['fullname'] = ($data['address_tag'] !== "") ? $this->getFullName() : "";
            $data['telephone'] = !is_null($this->getAddress()->getTelephone()) ? $this->getAddress()->getTelephone() : '08';
            $data['district_id'] = $this->getAddress()->getCustomAttribute('district') ?
                $this->getAddress()->getCustomAttribute('district')->getValue() : null;
            $data['city_id'] = $this->getAddress()->getCity();
        }

        /** @var \Trans\Customer\ViewModel\CustomerAddress $viewModel */
        if ($viewModel = $this->getData('view_model')) {
            $data['city_name'] = $viewModel->getCityName($data['city_id']);
            $data['district_name'] = $viewModel->getDistrictName($data['district_id']);
        }

        return new \Magento\Framework\DataObject($data);
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        /** @var \Trans\AllowLocation\Block\Locationallow $block */
        if ($block = $this->createBlock(\Trans\AllowLocation\Block\Locationallow::class)) {
            return $block->getSecret();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getLocationJsonData()
    {
        $location = $this->getLocation();

        return \Zend_Json_Encoder::encode([
            'pinpoint_location' => $location['pinpoint'] ?? '',
            'latitude'          => $location['lat'] ?? '',
            'longitude'         => $location['long'] ?? ''
        ]);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        if ($this->getRequest()->getParam('id')) {
            return __('Edit Address');
        } else {
            return __('Add Address');
        }
    }

    /**
     * @return bool
     */
    public function isEnableTelephone()
    {
        /** @var \Magento\Customer\Block\Widget\Telephone $block */
        if ($block = $this->createBlock(\Magento\Customer\Block\Widget\Telephone::class)) {
            return $block->isEnabled();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getFaxHtml()
    {
        /** @var \Magento\Customer\Block\Widget\Fax $block */
        $block = $this->createBlock(
            \Magento\Customer\Block\Widget\Fax::class,
            ['fax' => $this->getAddress()->getFax()]
        );

        if ($block && $block->isEnabled()) {
            return $block->toHtml();
        }

        return '';
    }

    /**
     * @return \Magento\Customer\Helper\Address
     */
    public function getAddressHelper()
    {
        return $this->addressHelper;
    }

    /**
     * @param $attributeCode
     *
     * @return string
     */
    public function getAttributeValidationClass($attributeCode)
    {
        try {
            return $this->addressHelper->getAttributeValidationClass($attributeCode);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param        $block
     * @param string $name
     * @param array  $data
     *
     * @return \Magento\Framework\View\Element\BlockInterface|null
     */
    protected function createBlock($block, $data = [], $name = '')
    {
        try {
            return $this->getLayout()->createBlock($block, $name, $data);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function isEditAddress()
    {
        $isEdit = 0;
        $customer = $this->getCustomer();
        if ($customer->getCustomAttribute('is_edit_address')) {
            $isEdit = $customer->getCustomAttribute('is_edit_address')->getValue();
        }
        return $isEdit;
    }
}

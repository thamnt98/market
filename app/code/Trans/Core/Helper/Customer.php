<?php
/**
 * @category Trans
 * @package  Trans_Customer
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @edited   J.P  <jaka.pondan@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Core\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Customer\Helper\View as CustomerViewHelper;

/**
 * Class Customer
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var Magento\Customer\Helper\View
     */
    protected $customerViewHelper;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param CustomerRegistry $customerRegistry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param CustomerViewHelper $customerViewHelper
     * @param DataObjectProcessor $dataProcessor
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        CustomerViewHelper $customerViewHelper,
        DataObjectProcessor $dataProcessor,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->dataProcessor = $dataProcessor;
        $this->customerViewHelper = $customerViewHelper;
        $this->eavConfig = $eavConfig;
    }

    /**
     * generate firstname lastname
     * 
     * @param string $fullname
     * @return array
     */
    public function generateFirstnameLastname(string $fullname)
    {
        $expl = explode(' ', $fullname);
        $data['firstname'] = $expl[0];
        $lastname = $data['firstname'];
        if(count($expl) > 1) {
            array_shift($expl);
            $lastname = implode(' ', $expl);
        }

        $data['lastname'] = $lastname;

        return $data;
    }

    /**
     * Generate fullname
     * 
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return string
     */
    public function generateFullnameByCustomer(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $fullname = $customer->getFirstname() . ' ' . $customer->getLastname();

        $expl = explode(' ', $fullname);

        $fullname = $this->generateFullnameByArray($expl);
        
        return $fullname;
    }

    /**
     * Generate fullname
     * 
     * @param array $customer
     * @return string
     */
    public function generateFullnameByArray(array $names)
    {
        // if customer name only 1 word
        if(count($names) == 2) {
            $names = array_unique($names);
        }
        
        $fullname = implode(' ', $names);
        
        return $fullname;
    }  

    /**
     * Create an object with data merged from Customer and CustomerSecure
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Model\Data\CustomerSecure
     */
    public function getFullCustomerObject($customer)
    {
        // No need to flatten the custom attributes or nested objects since the only usage is for email templates and
        // object passed for events
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }

    /**
     * Integration Customer & Central
     * @param $data
     * @param $id
     * @return mixed
     */
    public function setMagentoCustomerId($data,$id){

        if(!isset($data['magento_customer_id'])){
            return ;
        }
        if($id<1){
            return ;
        }
        $data['magento_customer_id'] = $data['magento_customer_id']."-".$id;
        return $data;
    }

    /**
     * Integration Customer & Central
     * Devide Id Customer & Datavalue
     * @param $data
     * @param $id
     * @return mixed
     */
    public function getCustValueId($magentoCustomerId,$id=0){

        if(empty($magentoCustomerId)){
            return;
        }
        $idArray = explode('-',$magentoCustomerId);
        if(!isset($idArray[$id])){
            return;
        }
        return $idArray[$id];
    }

    /**
     * validate phone number
     *
     * @param string $value
     * @throws \Exception
     * @return boolean
     */
    public function validatePhoneNumber($value)
    {
        if (!is_numeric($value)) {
            throw new \Exception("Phone Number must be numeric", 1);
        }

        return true;
    }

    /**
     * get gender options
     *
     * @return array
     */
    public function getGenderOptions()
    {
        $attribute = $this->eavConfig->getAttribute('customer', 'gender');
        $options = $attribute->getSource()->getAllOptions();
        return $options;
    }

    /**
     * get marital status options
     *
     * @return array
     */
    public function getMaritalStatusOptions()
    {
        $attribute = $this->eavConfig->getAttribute('customer', 'marital_status');
        $options = $attribute->getSource()->getAllOptions();
        return $options;
    }

    /**
     * get gender value by label
     *
     * @param string $label
     * @return int
     */
    public function getGenderValueByLabel(string $label)
    {
        $options = $this->getGenderOptions();

        if ($options) {
            foreach ($options as $option) {
                if ($option['label'] == $label) {
                    return (int)$option['value'];
                }
            }
        }
    }

    /**
     * get gender value for CDB
     *
     * @param int $gender
     * @return string
     */
    public function getGenderValueForCdb(int $gender)
    {
        $options = $this->getGenderOptions();

        if ($options) {
            foreach ($options as $option) {
                if ($option['value'] == (string)$gender) {
                    switch ($option['label']) {
                        case 'Male':
                            return 'M';
                            break;
                        
                        case 'Female':
                            return 'F';
                            break;
                    }
                }
            }
        }
    }

    /**
     * get marital_status value for CDB
     *
     * @param int $status
     * @return string
     */
    public function getMaritalStatusValueForCdb($status)
    {
        $options = $this->getMaritalStatusOptions();

        if ($options) {
            foreach ($options as $option) {
                if ($option['value'] == (string)$status) {
                    return $option['label'];
                }
            }
        }
    }

    /**
     * get marital status value by label
     *
     * @param string $label
     * @return int
     */
    public function getMaritalStatusValueByLabel(string $label)
    {
        $options = $this->getMaritalStatusOptions();
        $label = ucfirst($label);

        if ($options) {
            foreach ($options as $option) {
                if ($option['label'] == $label) {
                    return (int)$option['value'];
                }
            }
        }
    }
}

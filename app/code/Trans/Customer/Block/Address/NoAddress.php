<?php 

namespace Trans\Customer\Block\Address;

use Magento\Customer\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class NoAddress
 */
class NoAddress extends \Magento\Framework\View\Element\Template
{
	public function __construct(
	    \Magento\Framework\View\Element\Template\Context $context,
	    array $data = []
	) {
	    parent::__construct($context, $data);
	}

	/**
	 * Prepare the Address Book section layout
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
	    $this->pageConfig->getTitle()->set(__('My Address'));
	    return parent::_prepareLayout();
	}

	/**
	 * Generate and return "New Address" URL
	 *
	 * @return string
	 * @since 102.0.1
	 */
	public function getAddAddressUrl(): string
	{
	    return $this->getUrl('customer/address/new', ['_secure' => true]);
	}
	
	/**
	 * Message if not have an address
	 *
	 * @return string
	 */
	public function noAddresses()
	{
	    return 'You haven\'t added an address, Please add an address.';
	}
}
<?php
/**
 * @category Trans
 * @package  Trans_Integration
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Integration\Ui\Component\Listing\Column;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class UserAdmin
 */
class UserAdmin extends Column
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
	protected $customerFactory;
    
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\User\Model\UserFactory $userFactory
     */
	public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
		\Magento\User\Model\UserFactory $userFactory,
        array $components = [], array $data = [])
    {
		$this->userFactory = $userFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
    
    /**
     * Prepare customer column
     * 
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {	
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as & $item) {
                $user = $this->userFactory->create()->load($item[$this->getData('name')]);
        		$item[$this->getData('name')] = $user->getName();
            }
        }

        return $dataSource;
    }
}
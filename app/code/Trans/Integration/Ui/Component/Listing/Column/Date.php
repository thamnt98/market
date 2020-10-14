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
 * Class Date
 */
class Date extends Column
{
    /**
     * @var \Trans\Integration\Helper\Data
     */
	protected $dataHelper;
    
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Trans\Integration\Helper\Data $dataHelper
     */
	public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
		\Trans\Core\Helper\Data $dataHelper,
        array $components = [], array $data = [])
    {
		$this->dataHelper = $dataHelper;
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
				$datetime = isset($item[$this->getData('name')]) ? $item[$this->getData('name')] : $item["created_at"];
                
				$item[$this->getData('name')] = $this->dataHelper->changeDateFormat($datetime);
				
            }
        }

        return $dataSource;
    }
}
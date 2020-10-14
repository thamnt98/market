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
use Trans\Integration\Api\Data\IntegrationChannelInterface;

/**
 * Class Status
 */
class Status extends Column
{
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
				$flag = (int)$item[$this->getData('name')];

                if($flag === IntegrationChannelInterface::CHANNEL_STATUS_ACTIVE) {
                    $flag = __('Enable');
                }

                if($flag === IntegrationChannelInterface::CHANNEL_STATUS_INACTIVE) {
                    $flag = __('Disable');
                }
                
                $item[$this->getData('name')] = ucfirst($flag);
				
            }
        }
        return $dataSource;
    }
}
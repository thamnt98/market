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
 * Class Channel
 */
class Channel extends Column
{
    /**
     * @var \Trans\Integration\Api\IntegrationChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Trans\Integration\Api\IntegrationChannelRepositoryInterface $channelRepository,
        array $components = [], array $data = [])
    {
        $this->channelRepository = $channelRepository;
        
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
				$chId = $item[$this->getData('name')];
                $channel = $this->channelRepository->getById($chId);

                $item[$this->getData('name')] = ucfirst($channel->getName());
				
            }
        }
        return $dataSource;
    }
}
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
 * Class Action
 */
class Action extends Column
{
    /**
     * @var \Magento\Framework\Url
     */
    protected $urlBuilder;
    
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\Url $urlBuilder
     * @param \Magento\Framework\Escaper $escaper
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        array $components = [], array $data = [])
    {
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;

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
            $currentUrl = $this->urlBuilder->getCurrentUrl();

            $config = 'channel';
            if (strpos($currentUrl, 'channelmethod') !== false) {
                $config = 'channelmethod';
            }

            foreach ($dataSource['data']['items'] as & $item) {
                // $config = 'channel';

                $viewUrl = $this->urlBuilder->getUrl('integration/' . $config . '/create', ['id' => $item['id']]);
                $deleteUrl = $this->urlBuilder->getUrl('integration/' . $config . '/delete', ['id' => $item['id']]);

                $title = isset($item['name']) ? $this->escaper->escapeHtml($item['name']) : $this->escaper->escapeHtml($item['tag']);
                
                $item[$this->getData('name')] = [
                    'view' => [
                        'href' => $viewUrl,
                        'label' => __('Edit'),
                    ],
                    'delete' => [
                        'href' => $deleteUrl,
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete %1', $title),
                            'message' => __('Are you sure you want to delete a %1 record?', $title)
                        ]
                    ]
                ];
            }
        }

        return $dataSource;
    }
}

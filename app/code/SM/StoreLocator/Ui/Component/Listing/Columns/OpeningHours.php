<?php

namespace SM\StoreLocator\Ui\Component\Listing\Columns;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class OpeningHours
 * @package SM\StoreLocator\Ui\Component\Listing\Columns
 */
class OpeningHours extends Column
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * ProductData constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Json $json
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Json $json,
        array $components = [],
        array $data = []
    ) {
        $this->json = $json;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $fieldName = 'opening_hours';
                if (isset($item[$fieldName]) && !empty($item[$fieldName])) {
                    $item[$fieldName . '_html'] = "<a href='javascript:;'>View Opening Hours</a>";
                    $item[$fieldName . '_title'] = __('Opening Hours');
                    $item[$fieldName] = $this->json->unserialize($item[$fieldName]);
                }
            }
        }

        return $dataSource;
    }
}

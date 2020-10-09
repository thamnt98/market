<?php
declare(strict_types=1);

namespace SM\DigitalProduct\Ui\Component\Listing\Column\Category;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use SM\DigitalProduct\Api\Data\CategoryInterface;

/**
 * Class Actions
 * @package SM\DigitalProduct\Ui\Component\Listing\Column\Category
 */
class Actions extends Column
{
    const URL_PATH_EDIT = 'sm_digitalproduct/category/edit';
    const URL_PATH_DELETE = 'sm_digitalproduct/category/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
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
                if (isset($item['category_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    CategoryInterface::CATEGORY_ID => $item[CategoryInterface::CATEGORY_ID]
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    CategoryInterface::CATEGORY_ID => $item[CategoryInterface::CATEGORY_ID]
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete category'),
                                'message' => __('Are you sure you wan\'t to delete this record?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}

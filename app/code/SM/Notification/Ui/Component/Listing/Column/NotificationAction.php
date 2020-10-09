<?php

namespace SM\Notification\Ui\Component\Listing\Column;

class NotificationAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** Url path */
    const CREATE_NEW_PATH  = 'notification/index/new';
    const VIEW_DETAIL_PATH = 'notification/index/view';
    const DELETE_PATH      = 'notification/index/delete';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @param \Magento\Framework\Escaper                                   $escaper
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory
     * @param \Magento\Framework\UrlInterface                              $urlBuilder
     * @param array                                                        $components
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $name = $this->getData('name');
            if (isset($item['id'])) {
                $item[$name]['view'] = [
                    'href'          => $this->urlBuilder->getUrl(self::VIEW_DETAIL_PATH, ['id' => $item['id']]),
                    'label'         => __('View Detail'),
                    '__disableTmpl' => true,
                ];
                $title = $this->escaper->escapeHtml($item['title']);
                $item[$name]['delete'] = [
                    'href'          => $this->urlBuilder->getUrl(self::DELETE_PATH, ['id' => $item['id']]),
                    'label'         => __('Delete'),
                    'confirm'       => [
                        'title'         => __('Delete %1', $title),
                        'message'       => __('Are you sure you want to delete a %1 record?', $title),
                        '__disableTmpl' => true,
                    ],
                    'post'          => true,
                    '__disableTmpl' => true,
                ];
                $item[$name]['duplicate'] = [
                    'href'          => $this->urlBuilder->getUrl(self::CREATE_NEW_PATH, ['id' => $item['id']]),
                    'label'         => __('Duplicate'),
                    '__disableTmpl' => true,
                ];
            }
        }

        return $dataSource;
    }
}

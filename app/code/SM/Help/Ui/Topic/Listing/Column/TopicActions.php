<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\Help\Ui\Topic\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use SM\Help\Api\Data\TopicInterface;

/**
 * Class TopicActions
 * @package SM\Help\Ui\Topic\Listing\Column
 */
class TopicActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    const TOPIC_URL_PATH_EDIT = 'sm_help/topic/edit';
    const TOPIC_URL_PATH_DELETE = 'sm_help/topic/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * TopicActions constructor.
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
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item[TopicInterface::ID])) {
                    $item[$name]['edit'] = [
                        'href'  => $this->urlBuilder->getUrl(self::TOPIC_URL_PATH_EDIT, [
                            TopicInterface::ID => $item[TopicInterface::ID],
                        ]),
                        'label' => __('Edit'),
                    ];
                    $item[$name]['delete'] = [
                        'href'  => $this->urlBuilder->getUrl(self::TOPIC_URL_PATH_DELETE, [
                            TopicInterface::ID => $item[TopicInterface::ID],
                        ]),
                        'label' => __('Delete'),
                    ];
                }
            }
        }

        return $dataSource;
    }
}

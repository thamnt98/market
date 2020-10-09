<?php
/**
 * @category Magento
 * @package SM\DigitalProduct\Ui\Component\Listing\Column\Category
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author Hung Viet Nguyen <hungnv6@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\DigitalProduct\Ui\Component\Listing\Column\OperatorIcon;

use Magento\Catalog\Helper\Image;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use SM\DigitalProduct\Api\Data\CategoryInterface;
use SM\DigitalProduct\Api\Data\OperatorIconInterface;

/**
 * Class Thumbnail
 * @package SM\DigitalProduct\Ui\Component\Listing\Column\Category
 */
class Icon extends Column
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param Image $imageHelper
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        Image $imageHelper,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->imageHelper = $imageHelper;
        $this->storeManager = $storeManager;
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
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[OperatorIconInterface::ICON])) {
                    $imageParts = json_decode($item[OperatorIconInterface::ICON], true);
                    $url = $imageParts["url"];
                    $alt = $imageParts["name"];
                } else {
                    $url = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail_image');
                    $alt = "";
                }
                $item[$fieldName . '_src'] = $url;
                $item[$fieldName . '_alt'] = $alt;
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'sm_digitalproduct/operatoricon/edit',
                    ['operator_icon_id' => $item['operator_icon_id']]
                );
                $item[$fieldName . '_orig_src'] = $url;
            }
        }

        return $dataSource;
    }
}

<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Block\Adminhtml\Category;

class BrandList extends \Magento\Backend\Block\Template
{
    const FIELD_NAME          = 'category_brand_list';
    const OLD_DATA_FIELD_NAME = 'category_brand_list_old_data';

    /**
     * @var \SM\Brand\Model\Category\BrandListFactory
     */
    protected $brandListFactory;

    /**
     * FilterList constructor.
     *
     * @param \SM\Brand\Model\Category\BrandListFactory              $brandListFactory
     * @param \Magento\Backend\Block\Template\Context                $context
     * @param array                                                  $data
     */
    public function __construct(
        \SM\Brand\Model\Category\BrandListFactory $brandListFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->brandListFactory = $brandListFactory;
    }

    /**
     * @return string
     */
    public function getDialogUrl()
    {
        return $this->getUrl(
            'sm_brand/category/addBrand',
            [
                'componentJson' => true
            ]
        );
    }

    /**
     * @return string|int
     */
    public function getCategoryId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return self::FIELD_NAME;
    }

    /**
     * @return string
     */
    public function getPositionDataJson()
    {
        /** @var \SM\Brand\Model\ResourceModel\Category\BrandList $resource */
        $resource = $this->brandListFactory->create()->getResource();

        return \Zend_Json::encode($resource->getBrandPosition($this->getCategoryId()));
    }
}

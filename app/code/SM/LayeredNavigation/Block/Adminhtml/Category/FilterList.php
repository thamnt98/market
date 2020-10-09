<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_LayeredNavigation
 *
 * Date: April, 27 2020
 * Time: 10:34 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\LayeredNavigation\Block\Adminhtml\Category;

class FilterList extends \Magento\Backend\Block\Template
{
    const FIELD_NAME          = 'category_filter_list';
    const OLD_DATA_FIELD_NAME = 'category_filter_list_old_data';

    /**
     * @var \SM\LayeredNavigation\Model\Category\FilterListFactory
     */
    protected $filterListFactory;

    /**
     * FilterList constructor.
     *
     * @param \SM\LayeredNavigation\Model\Category\FilterListFactory $filterListFactory
     * @param \Magento\Backend\Block\Template\Context                $context
     * @param array                                                  $data
     */
    public function __construct(
        \SM\LayeredNavigation\Model\Category\FilterListFactory $filterListFactory,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->filterListFactory = $filterListFactory;
    }

    /**
     * @return string
     */
    public function getDialogUrl()
    {
        return $this->getUrl(
            'smlayer/category/addAttribute',
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
        /** @var \SM\LayeredNavigation\Model\ResourceModel\Category\FilterList $resource */
        $resource = $this->filterListFactory->create()->getResource();

        return \Zend_Json::encode($resource->getAttributesPosition($this->getCategoryId()));
    }
}

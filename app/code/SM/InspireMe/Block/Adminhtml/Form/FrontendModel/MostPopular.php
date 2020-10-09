<?php
/**
 * @project    ct-corp-transmart
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author     Duc Nguyen Hoang <ducnh2@smartosc.com>
 * @copyright  Copyright © 2020 SmartOSC. All rights reserved.
 * @url        http://www.smartosc.com
 */

namespace SM\InspireMe\Block\Adminhtml\Form\FrontendModel;

use SM\InspireMe\Helper\Data;

/**
 * Class MostPopular
 * @package SM\InspireMe\Block\Adminhtml\Form\FrontendModel
 */
class MostPopular extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var string
     */
    protected $_template = 'SM_InspireMe::system/config/form/field/most_popular.phtml';

    /**
     * Prepare to render.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            Data::MP_POSITION,
            [
                'label' => __('Position'),
            ]
        );
        $this->addColumn(
            Data::MP_BASED_ON,
            [
                'label' => __('Based On'),
            ]
        );
        $this->addColumn(
            Data::MP_SELECT_ARTICLE,
            [
                'label' => __('Article Name'),
            ]
        );
        $this->addColumn(
            Data::MP_SELECT_ARTICLE_ID,
            [
                'label' => __('Article ID'),
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * @param $field
     * @param $position
     * @return string
     */
    public function getFieldName($field, $position)
    {
        return $this->getElement()->getName() . "[$position][$field]";
    }

    /**
     * @return array
     */
    public function getBasedOnOptions()
    {
        return [
            [
                'value' => 0,
                'label' => __('Most Viewed'),
            ],
            [
                'value' => 1,
                'label' => __('Select Article'),
            ],
        ];
    }
}

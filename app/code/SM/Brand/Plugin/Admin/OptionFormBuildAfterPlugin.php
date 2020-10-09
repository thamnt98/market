<?php

/**
 * @category SM
 * @package SM_Brand
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @author      Chinhvd <chinhvd@smartosc.com>
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

namespace SM\Brand\Plugin\Admin;

use Amasty\ShopbyBrand\Observer\Admin\OptionFormBuildAfter;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Cms\Model\Page\Source\PageLayout;
use SM\Brand\Model\Ui\Source\ListCampaign;

/**
 * Class OptionFormBuildAfterPlugin
 */
class OptionFormBuildAfterPlugin
{
    /**
     * @var PageLayout
     */
    protected $pageLayout;

    /**
     * @var ListCampaign
     */
    protected $campaign;

    /**
     * @param PageLayout $pageLayout
     * @param ListCampaign $campaign
     */
    public function __construct(
        PageLayout $pageLayout,
        ListCampaign $campaign
    ) {
        $this->campaign = $campaign;
        $this->pageLayout = $pageLayout;
    }

    /**
     *
     * @param OptionFormBuildAfter $subject
     * @param $result
     * @param EventObserver $observer
     * @return array|mixed|null
     */
    public function afterExecute(OptionFormBuildAfter $subject, $result, EventObserver $observer)
    {
        $result = $observer->getData('form');
        $this->addProductListFieldset($result);
        return $result;
    }

    /**
     *
     * @param \Magento\Framework\Data\Form $form
     */
    public function addProductListFieldset(\Magento\Framework\Data\Form $form)
    {
        $form->getElements()->remove('featured_fieldset');

        $layoutFieldset = $form->addFieldset(
            'brand_page_layout_fieldset',
            [
                'legend' => __('Brand Page Layout'),
                'class'  => 'form-inline'
            ]
        );

        $listLayout = $this->pageLayout->toOptionArray();

        $layoutFieldset->addField(
            'brand_page_layout',
            'select',
            [
                'name' => 'brand_page_layout',
                'label' => __('Layout'),
                'title' => __('Layout'),
                'values' => $listLayout
            ]
        );

        $layoutFieldset->addField(
            'most_popular_category_id',
            'text',
            [
                'name' => 'most_popular_category_id',
                'label' => __('Most Popular Category Id For Mobile'),
                'title' => __('Most Popular Category Id For Mobile'),
                'note'  => __('Pls fill only one category id to define which category is most popular in brand page')
            ]
        );

        $listCampaign = $this->campaign->toOptionArray();

        $layoutFieldset->addField(
            'campaign_id',
            'select',
            [
                'name' => 'campaign_id',
                'label' => __('Campaign Page For Mobile'),
                'title' => __('Campaign Page For Mobile'),
                'values' => $listCampaign
            ]
        );
    }
}

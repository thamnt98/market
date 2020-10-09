<?php
/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

declare(strict_types=1);


namespace SM\Catalog\Plugin\Catalog\Block\Adminhtml\Product\Attribute\Edit;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tabs as MagentoAttributeEditTabs;

/**
 * Class Tabs
 * @package SM\Catalog\Plugin\Catalog\Block\Adminhtml\Product\Attribute\Edit
 */
class Tabs
{
    /**
     * @param MagentoAttributeEditTabs $subject
     * @return array
     */
    public function beforeToHtml(MagentoAttributeEditTabs $subject)
    {
        $content = $subject->getChildHtml('sm_specification');
        $subject->addTabAfter(
            'sm_specifications',
            [
                'label' => __('Show On Specifications'),
                'title' => __('Show On Specifications'),
                'content' => $content,
            ],
            'front'
        );

        return [];
    }
}

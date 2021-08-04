<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Category
 *
 * Date: June, 16 2021
 * Time: 9:41 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Category\Plugin\Magento\Catalog\Model\Category\Attribute\Backend;

class Image
{
    /**
     * @param \Magento\Catalog\Model\Category\Attribute\Backend\Image $subject
     * @param callable                                                $proceed
     * @param \Magento\Framework\DataObject                           $object
     *
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
     */
    public function aroundBeforeSave(
        \Magento\Catalog\Model\Category\Attribute\Backend\Image $subject,
        callable $proceed,
        $object
    ) {
        $attributeName = $subject->getAttribute()->getName();
        $value = $object->getData($attributeName);

        $result = $proceed($object);

        if ($object instanceof \Magento\Catalog\Model\Category && isset($value[0]['name'])) {
            $object->setData($attributeName, $value[0]['name']);
        }

        return $result;
    }
}

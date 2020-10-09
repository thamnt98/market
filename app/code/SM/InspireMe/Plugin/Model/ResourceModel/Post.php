<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_InspireMe
 *
 * Date: March, 31 2020
 * Time: 2:29 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\InspireMe\Plugin\Model\ResourceModel;

class Post
{
    public function beforeSave(
        \Mirasvit\Blog\Model\ResourceModel\Post $subject,
        \Magento\Framework\Model\AbstractModel $object
    ) {
        if ($object->getData('status') == \Mirasvit\Blog\Api\Data\PostInterface::STATUS_PUBLISHED) {
            if ($object->getOrigData('status') != $object->getData('status') ||
                empty($object->getData('published_date'))
            ) {
                $object->setData('published_date', date('Y-m-d'));
            }
        } else {
            $object->setData('published_date', null);
        }

        return [$object];
    }
}

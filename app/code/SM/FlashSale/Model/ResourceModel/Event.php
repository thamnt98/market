<?php

namespace SM\FlashSale\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Event extends \Magento\CatalogEvent\Model\ResourceModel\Event
{

    /**
     * After model save (save event image)
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $where = [$object->getIdFieldName() . '=?' => $object->getId(), 'store_id = ?' => $object->getStoreId()];

        $connection = $this->getConnection();
        $connection->delete($this->getTable('magento_catalogevent_event_image'), $where);

        if ($object->getImage() !== null) {
            $data = [
                $object->getIdFieldName() => $object->getId(),
                'store_id' => $object->getStoreId(),
                'image' => $object->getImage(),
                'terms_conditions' => $object->getData('terms_conditions'),
                'mb_short_title' => $object->getData('mb_short_title'),
                'mb_title' => $object->getData('mb_title'),
                'mb_short_description' => $object->getData('mb_short_description')
            ];

            $connection->insert($this->getTable('magento_catalogevent_event_image'), $data);
        }
        return $this;
    }

    /**
     * After model load (loads event image)
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('magento_catalogevent_event_image'),
            [
                'type' => $connection->getCheckSql('store_id = 0', "'default'", "'store'"),
                'image',
                'terms_conditions',
                'mb_short_title',
                'mb_title',
                'mb_short_description'
            ]
        )->where(
            $object->getIdFieldName() . '=?',
            $object->getId()
        )->where(
            'store_id IN (0, ?)',
            $object->getStoreId()
        );

        $data = $connection->fetchAssoc($select);


        if (isset($data['store'])) {
            $object->setImage($data['store']['image']);
            $object->setImageDefault(
                isset($data['default']['image']) ? $data['default']['image'] : ''
            );

            $object->setTermsConditions($data['store']['terms_conditions']);
            $object->setTermsConditionsDefault(
                isset($data['default']['terms_conditions']) ? $data['default']['terms_conditions'] : ''
            );

            $object->setMbShortTitle($data['store']['mb_short_title']);
            $object->setMbShortTitleDefault(
                isset($data['default']['mb_short_title']) ? $data['default']['mb_short_title'] : ''
            );

            $object->setMbTitle($data['store']['mb_title']);
            $object->setMbTitleDefault(
                isset($data['default']['mb_title']) ? $data['default']['mb_title'] : ''
            );

            $object->setMbShortDescription($data['store']['mb_short_description']);
            $object->setMbShortDescriptionDefault(
                isset($data['default']['mb_short_description']) ? $data['default']['mb_short_description'] : ''
            );
        }

        if (isset($data['default']) && !isset($data['store'])) {
            $object->setImage($data['default']['image']);
            $object->setTermsConditions($data['default']['terms_conditions']);
            $object->setMbShortTitle($data['default']['mb_short_title']);
            $object->setMbTitle($data['default']['mb_title']);
            $object->setMbShortDescription($data['default']['mb_short_description']);
        }

        return $this;
    }
}

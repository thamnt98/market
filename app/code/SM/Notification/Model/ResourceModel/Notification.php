<?php

/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 07 2020
 * Time: 6:51 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\ResourceModel;

use SM\Notification\Model\Source\CustomerType as CustomerTypeOptions;

class Notification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Table name
     */
    const TABLE_NAME = 'sm_notification_msg';

    /**
     * Join table alias
     */
    const PUSH_TABLE_ALIAS     = 'push';
    const SMS_TABLE_ALIAS      = 'sms';
    const EMAIL_TABLE_ALIAS    = 'email';
    const CUSTOMER_TABLE_ALIAS = 'customer';

    /**
     * Join column alias
     */
    const PUSH_TITLE_ALIAS        = 'push_title';
    const PUSH_CONTENT_ALIAS      = 'push_content';
    const EMAIL_SUBJECT_ALIAS     = 'email_subject';
    const EMAIL_TEMPLATE_ID_ALIAS = 'email_template';
    const EMAIL_PARAMS_ALIAS      = 'email_params';
    const SMS_CONTENT_ALIAS       = 'sms';
    const CUSTOMER_IDS_ALIAS      = 'customer_ids';

    /**
     * Initialize resource
     */
    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'id');
    }

    /**
     * @override
     *
     * @param string                                 $field
     * @param mixed                                  $value
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinLeft(
            [self::PUSH_TABLE_ALIAS => Push::TABLE_NAME],
            $this->getMainTable() . '.id = ' . self::PUSH_TABLE_ALIAS . '.message_id',
            [
                self::PUSH_CONTENT_ALIAS => self::PUSH_TABLE_ALIAS . '.content',
                self::PUSH_TITLE_ALIAS   => self::PUSH_TABLE_ALIAS . '.title',
            ]
        )->joinLeft(
            [self::EMAIL_TABLE_ALIAS => Email::TABLE_NAME],
            $this->getMainTable() . '.id = ' . self::EMAIL_TABLE_ALIAS . '.message_id',
            [
                self::EMAIL_SUBJECT_ALIAS     => self::EMAIL_TABLE_ALIAS . '.subject',
                self::EMAIL_TEMPLATE_ID_ALIAS => self::EMAIL_TABLE_ALIAS . '.template_id',
                self::EMAIL_PARAMS_ALIAS      => self::EMAIL_TABLE_ALIAS . '.params',
            ]
        )->joinLeft(
            [self::SMS_TABLE_ALIAS => Sms::TABLE_NAME],
            $this->getMainTable() . '.id = ' . self::SMS_TABLE_ALIAS . '.message_id',
            [
                self::SMS_CONTENT_ALIAS => self::SMS_TABLE_ALIAS . '.content',
            ]
        )->joinLeft(
            [self::CUSTOMER_TABLE_ALIAS => CustomerMessage::TABLE_NAME],
            $this->getMainTable() . '.id = ' . self::CUSTOMER_TABLE_ALIAS . '.message_id',
            [
                "GROUP_CONCAT(" . self::CUSTOMER_TABLE_ALIAS . ".customer_id SEPARATOR ',') AS "
                . self::CUSTOMER_IDS_ALIAS,
            ]
        );

        return $select;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\SM\Notification\Model\Notification $object
     *
     * @return Notification
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getStartDate()) {
            $object->setData('created_at', $object->getStartDate());
        }

        if (is_array($object->getParams())) {
            $object->setParams(json_encode($object->getParams()));
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\SM\Notification\Model\Notification $object
     *
     * @return Notification
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->saveCustomerMessage($object)
            ->saveSms($object)
            ->saveEmail($object)
            ->savePushContent($object);

        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\SM\Notification\Model\Notification $object
     *
     * @return Notification
     */
    protected function savePushContent(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getPushContent()) {
            $this->getConnection()
                ->insertOnDuplicate(
                    Push::TABLE_NAME,
                    [
                        'message_id' => $object->getId(),
                        'title'      => strip_tags($object->getPushTitle()),
                        'content'    => strip_tags($object->getPushContent()),
                    ],
                    ['content', 'title']
                );
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\SM\Notification\Model\Notification $object
     *
     * @return Notification
     */
    protected function saveSms(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getSms()) {
            $this->getConnection()
                ->insertOnDuplicate(
                    Sms::TABLE_NAME,
                    [
                        'message_id' => $object->getId(),
                        'content'    => $object->getSms(),
                    ],
                    ['content']
                );
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\SM\Notification\Model\Notification $object
     *
     * @return Notification
     */
    protected function saveEmail(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getEmailTemplate()) {
            $params = $object->getEmailParams();
            if ($params && is_array($params)) {
                $params = json_encode($params);
            }
            $this->getConnection()
                ->insertOnDuplicate(
                    Email::TABLE_NAME,
                    [
                        'message_id'  => $object->getId(),
                        'subject'     => $object->getEmailSubject(),
                        'template_id' => $object->getEmailTemplate(),
                        'params'      => $params,
                    ],
                    ['subject', 'template_id', 'params']
                );
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\SM\Notification\Model\Notification $object
     *
     * @return Notification
     */
    public function saveCustomerMessage(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($customerIds = $this->getCustomerIds($object)) {
            $this->getConnection()
                ->delete(
                    CustomerMessage::TABLE_NAME,
                    'message_id = ' . $object->getId()
                );

            $data = [];
            foreach ($customerIds as $customerId) {
                $data[] = [
                    'message_id'  => $object->getId(),
                    'customer_id' => $customerId,
                ];
            }

            if ($data) {
                $this->getConnection()->insertMultiple(CustomerMessage::TABLE_NAME, $data);
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel|\SM\Notification\Model\Notification $object
     *
     * @return Notification
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setParams(json_decode($object->getParams(), true));
        $select = $this->getConnection()
            ->select()
            ->from(CustomerMessage::TABLE_NAME, ['customer_id'])
            ->where('message_id = ?', $object->getId());
        $object->setData('customer_ids', $this->getConnection()->fetchCol($select));

        return parent::_afterLoad($object);
    }

    /**
     * @param $segments
     *
     * @return array
     */
    protected function getCustomerIdBySegment($segments)
    {
        $result = [];

        if (!is_array($segments)) {
            $segments = explode(',', $segments);
        }

        foreach ($segments as $segment) {
            $select = $this->getConnection()->select();
            $select->from(
                ['cs' => 'magento_customersegment_customer'],
                'customer_id'
            )->joinInner(
                ['c' => 'customer_entity'],
                'c.entity_id = cs.customer_id',
                []
            )->joinInner(
                ['s' => 'magento_customersegment_segment'],
                'cs.segment_id = s.segment_id',
                []
            )->where(
                'c.is_active = ?',
                1
            )->where(
                's.is_active = ?',
                1
            )->where(
                'cs.segment_id = ?',
                $segment
            );

            $result = array_merge($result, $this->getConnection()->fetchCol($select));
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getAllCustomerIds()
    {
        $select = $this->getConnection()->select();
        $select->from(['customer_entity'], 'entity_id')
            ->where('is_active = ? ', 1);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return array
     */
    protected function getCustomerIds(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getData('admin_type') != $object->getOrigData('admin_type')) {
            switch ($object->getData('admin_type')) {
                case CustomerTypeOptions::TYPE_ALL:
                    $customerIds = $this->getAllCustomerIds();
                    break;
                case CustomerTypeOptions::TYPE_CUSTOMER_SEGMENT:
                    $customerIds = $this->getCustomerIdBySegment($object->getData('segment_ids'));
                    break;
                default:
                    $customerIds = $object->getData('customer_ids');
            }
        } elseif ($object->getData('customer_ids')) {
            $customerIds = $object->getData('customer_ids');
        } else {
            return [];
        }

        if (is_array($customerIds)) {
            $customerIds = implode(',', $customerIds);
        }

        $select = $this->getConnection()->select();
        $select->from(['customer_entity'], 'entity_id')
            ->where("entity_id IN ({$customerIds})");

        return $this->getConnection()->fetchCol($select);
    }
}

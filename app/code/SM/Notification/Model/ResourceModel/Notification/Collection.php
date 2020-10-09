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

namespace SM\Notification\Model\ResourceModel\Notification;

use Magento\Framework\DB\Select;
use SM\Notification\Model\Notification as Model;
use SM\Notification\Model\ResourceModel\Sms;
use SM\Notification\Model\ResourceModel\CustomerMessage;
use SM\Notification\Model\ResourceModel\Email;
use SM\Notification\Model\ResourceModel\Notification as Resource;
use SM\Notification\Model\ResourceModel\Push;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _construct()
    {
        $this->_init(Model::class, Resource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinLeft(
                [Resource::PUSH_TABLE_ALIAS => Push::TABLE_NAME],
                'main_table.id = ' . Resource::PUSH_TABLE_ALIAS . '.message_id',
                [
                    Resource::PUSH_CONTENT_ALIAS => Resource::PUSH_TABLE_ALIAS . '.content',
                    Resource::PUSH_TITLE_ALIAS   => Resource::PUSH_TABLE_ALIAS . '.title',
                ]
            )->joinLeft(
                [Resource::EMAIL_TABLE_ALIAS => Email::TABLE_NAME],
                'main_table.id = ' . Resource::EMAIL_TABLE_ALIAS . '.message_id',
                [
                    Resource::EMAIL_SUBJECT_ALIAS     => Resource::EMAIL_TABLE_ALIAS . '.subject',
                    Resource::EMAIL_TEMPLATE_ID_ALIAS => Resource::EMAIL_TABLE_ALIAS . '.template_id',
                    Resource::EMAIL_PARAMS_ALIAS      => Resource::EMAIL_TABLE_ALIAS . '.params',
                ]
            )->joinLeft(
                [Resource::SMS_TABLE_ALIAS => Sms::TABLE_NAME],
                'main_table.id = ' . Resource::SMS_TABLE_ALIAS . '.message_id',
                [
                    Resource::SMS_CONTENT_ALIAS => Resource::SMS_TABLE_ALIAS . '.content',
                ]
            )->joinLeft(
                [Resource::CUSTOMER_TABLE_ALIAS => CustomerMessage::TABLE_NAME],
                'main_table.id = ' . Resource::CUSTOMER_TABLE_ALIAS . '.message_id',
                [
                    "GROUP_CONCAT(" . Resource::CUSTOMER_TABLE_ALIAS . ".customer_id SEPARATOR ',') AS "
                    . Resource::CUSTOMER_IDS_ALIAS,
                ]
            )->group('main_table.id');

        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if (is_array($field)) {
            $conditions = [];
            foreach ($field as $key => $value) {
                $conditions[] = $this->_translateCondition($value, isset($condition[$key]) ? $condition[$key] : null);
            }

            $resultCondition = '(' . implode(') ' . Select::SQL_OR . ' (', $conditions) . ')';
        } else {
            if (strpos($field, '.') === false) {
                $field = 'main_table.' . $field;
            }

            $resultCondition = $this->_translateCondition($field, $condition);
        }

        $this->_select->where($resultCondition, null, Select::TYPE_CONDITION);

        return $this;
    }
}

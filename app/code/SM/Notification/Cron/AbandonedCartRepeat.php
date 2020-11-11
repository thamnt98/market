<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 16 2020
 * Time: 10:49 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Cron;

class AbandonedCartRepeat extends AbandonedCart
{
    /**
     * @override
     * @return array
     */
    protected function getAbandonedCart()
    {
        $time = (int)$this->settingHelper->getConfigValue('sm_notification/generate/abandoned_cart_repeat_time');
        $select = $this->connection->select();
        $select->from(
            ['q' => 'quote'],
            []
        )->joinInner(
            ['c' => 'customer_entity'],
            'q.customer_id = c.entity_id',
            []
        )->joinInner(
            ['n' => \SM\Notification\Model\ResourceModel\TriggerEvent::TABLE_NAME],
            'q.entity_id = n.event_id AND event_name = \'' . self::EVENT_NAME . '\'',
            []
        )->where(
            'q.is_active = ?',
            1
        )->where(
            'n.event_name = ?',
            self::EVENT_NAME
        )->where(
            'current_timestamp() >= DATE_ADD(' .
            '(SELECT created_at FROM sm_notification_trigger_event ' .
            'WHERE event_id = q.entity_id AND event_name = \'' . self::EVENT_NAME . '\' ' .
            'ORDER BY created_at DESC LIMIT 1
            ), INTERVAL ' . $time . ' day)'
        )->limit(50);

        $select->columns([
            'q.entity_id',
            'q.customer_id',
            'q.store_id',
            '(SELECT product_id FROM quote_item WHERE quote_id = q.entity_id ORDER BY item_id DESC LIMIT 1)' .
            ' as product_id'
        ]);

        return $this->connection->fetchAssoc($select);
    }

    /**
     * @return string
     */
    protected function getLockFileName()
    {
        return 'sm_notification_abandoned_cart_repeat.lock';
    }
}

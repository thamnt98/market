<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: June, 04 2020
 * Time: 11:14 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Notification\Consumer;

use SM\Notification\Model\Sms as Model;

class Sms extends AbstractConsumer
{
    public function process(\SM\Notification\Api\Data\Queue\SmsInterface $request)
    {
        try {
            $customer = $this->getCustomer($request->getCustomerId());
            if (!$this->validate($customer->getId(), $request->getEvent(), Model::NOTIFICATION_TYPE)) {
                $this->logError(
                    "Consumer `SMS` Error:\n\tCustomer isn't selected sms.\n",
                    $request->getData()
                );

                return;
            }

            $telephone = $customer->getCustomAttribute('telephone');
            if ($telephone) {
                $result = $this->integration->sendSms($telephone->getValue(), $request->getContent());
                $this->logInfo(
                    "Consumer `SMS` Success\n",
                    [
                        'params' => $request->getData(),
                        'result' => $result
                    ]
                );
            } else {
                $this->logError(
                    "Consumer `SMS` Error:\n\tTelephone is empty.\n",
                    $request->getData()
                );
            }
        } catch (\Exception $e) {
            $this->reSyncUpdate($request->getId());
            $this->logError(
                "Consumer `SMS` Error:\n\t" . $e->getMessage() . "\n",
                $request->getData()
            );
        }
    }

    protected function reSyncUpdate($id)
    {
        // todo turn off re-sync : call push noti response code != 200 but response data success
        return;
        $this->connection->update(
            \SM\Notification\Model\ResourceModel\CustomerMessage::TABLE_NAME,
            ['sms_status' => \SM\Notification\Model\Notification::SYNC_PENDING],
            "id = {$id}"
        );
    }
}

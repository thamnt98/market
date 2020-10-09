<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_MyVoucher
 *
 * Date: July, 04 2020
 * Time: 11:26 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\MyVoucher\Plugin\MagentoFramework\EntityManager\Operation\Create;

class CreateMain
{
    /**
     * @param        $subject
     * @param object $entity
     * @param array  $arguments
     *
     * @return array
     */
    public function beforeExecute(
        $subject,
        $entity,
        $arguments = []
    ) {
        if ($entity instanceof \Magento\SalesRule\Model\Rule &&
            $entity->getData('staging')
        ) {
            $stagingData = $entity->getData('staging');
            if (isset($stagingData['start_time'])) {
                $arguments['from_date'] = $stagingData['start_time'];
            }

            if (isset($stagingData['end_time'])) {
                $arguments['to_date'] = $stagingData['end_time'];
            }
        }

        return [$entity, $arguments];
    }
}

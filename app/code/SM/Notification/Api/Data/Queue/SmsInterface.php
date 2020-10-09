<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 15 2020
 * Time: 9:55 AM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Api\Data\Queue;

interface SmsInterface extends GeneralInterface
{
    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();
}

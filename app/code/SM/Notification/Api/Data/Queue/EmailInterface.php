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

interface EmailInterface extends GeneralInterface
{
    /**
     * @param string $subject
     *
     * @return $this
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setTemplateId($id);

    /**
     * @return string
     */
    public function getTemplateId();

    /**
     * @param string[] $params
     *
     * @return $this
     */
    public function setParams($params);

    /**
     * @return string[]
     */
    public function getParams();
}

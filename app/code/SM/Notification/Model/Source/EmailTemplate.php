<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Notification
 *
 * Date: September, 26 2020
 * Time: 2:13 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Notification\Model\Source;

class EmailTemplate extends \Magento\Config\Model\Config\Source\Email\Template implements
    \Magento\Framework\Data\OptionSourceInterface
{
    protected $options = null;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (is_null($this->options)) {
            /** @var $coll \Magento\Email\Model\ResourceModel\Template\Collection */
            $coll = $this->_templatesFactory->create();
            $coll->load();
            $this->options = $coll->toOptionArray();
        }

        return $this->options;
    }
}

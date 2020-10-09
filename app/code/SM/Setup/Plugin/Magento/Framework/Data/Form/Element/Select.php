<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Setup
 *
 * Date: June, 30 2020
 * Time: 3:50 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Setup\Plugin\Magento\Framework\Data\Form\Element;

class Select
{
    /**
     * @param \Magento\Framework\Data\Form\Element\Select $subject
     * @param                                             $result
     *
     * @return array
     */
    public function afterGetValues(
        \Magento\Framework\Data\Form\Element\Select $subject,
        $result
    ) {
        return $this->removeValueOptionsWrong($result);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\Select $subject
     * @param                                             $result
     * @param string                                      $key
     *
     * @return array
     */
    public function afterGetData(
        \Magento\Framework\Data\Form\Element\Select $subject,
        $result,
        $key = ''
    ) {
        if ($key === 'values') {
            return $this->removeValueOptionsWrong($result);
        }

        return $result;
    }

    /**
     * @param $options
     *
     * @return array
     */
    protected function removeValueOptionsWrong($options)
    {
        if (!is_array($options)) {
            return [];
        }

        foreach ($options as $key => $item) {
            if (!is_array($item)) {
                continue;
            }

            if (!isset($item['value']) || !isset($item['label'])) {
                unset($options[$key]);
            }
        }

        return $options;
    }
}

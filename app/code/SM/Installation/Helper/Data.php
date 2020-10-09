<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Installation
 *
 * Date: April, 21 2020
 * Time: 3:49 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Installation\Helper;

use SM\Installation\Block\Form as Installation;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_MODULE_ENABLE   = 'installation/general/enable';
    const XML_TOOLTIP_CONTENT = 'installation/general/tooltip';

    const QUOTE_OPTION_KEY = 'installation_service';

    const OPTION_YES = 1;
    const OPTION_NO  = 0;

    /**
     * @param string          $scopeType
     * @param null|string|int $scopeCode
     *
     * @return bool
     */
    public function isEnabled($scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return (bool)$this->scopeConfig->getValue(self::XML_MODULE_ENABLE, $scopeType, $scopeCode);
    }

    /**
     * @param string          $scopeType
     * @param null|string|int $scopeCode
     *
     * @return mixed
     */
    public function getTooltip($scopeType = \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(self::XML_TOOLTIP_CONTENT, $scopeType, $scopeCode);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     *
     * @throws \Zend_Json_Exception
     */
    public function updateInstallationItem(&$item)
    {
        $useInstallation = $this->_getRequest()->getParam(
            Installation::USED_FIELD_NAME
        );
        $installationNote = strip_tags($this->_getRequest()->getParam(
            Installation::NOTE_FIELD_NAME
        ));
        $fee = $this->_getRequest()->getParam(
            Installation::FEE_FIELD_NAME
        ) ?? 0;

        if ((int) $useInstallation === 0) {
            $useInstallation = $useInstallation === 'on' ? self::OPTION_YES : self::OPTION_NO;
        }

        $data = [
            self::QUOTE_OPTION_KEY => [
                Installation::USED_FIELD_NAME => $useInstallation,
                Installation::NOTE_FIELD_NAME => trim($installationNote),
                Installation::FEE_FIELD_NAME  => $fee
            ]
        ];
        $option = $item->getOptionByCode('info_buyRequest');
        if ($option) {
            $value = \Zend_Json_Decoder::decode($option->getValue());
            $value = array_merge($value, $data);
            $option->setValue(\Zend_Json_Encoder::encode($value, true));
        } else {
            $option = [
                'code' => 'info_buyRequest',
                'value' => \Zend_Json_Encoder::encode($data, true)
            ];
        }

        $item->addOption($option);
    }

    /**
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     *
     * @throws \Zend_Json_Exception
     */
    public function removeInstallationItem(&$item)
    {
        $option = $item->getOptionByCode('info_buyRequest');
        if ($option) {
            $value = \Zend_Json_Decoder::decode($option->getValue());
            unset($value[self::QUOTE_OPTION_KEY]);
            $option->setValue(\Zend_Json_Encoder::encode($value, true));
        }

        $item->addOption($option);
    }
}

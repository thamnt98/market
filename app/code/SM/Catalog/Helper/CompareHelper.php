<?php
declare(strict_types=1);

namespace SM\Catalog\Helper;

/**
 * Class Data
 *
 * @package SM\Catalog\Helper
 */
class CompareHelper extends \Magento\Framework\App\Helper\AbstractHelper{

    protected $_scopeConfig;

    protected $_httpRequest;

    public function __construct(\Magento\Framework\App\Helper\Context $context,
                                \Magento\Framework\App\Request\Http $httpRequest,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        parent::__construct($context);
        $this->_scopeConfig = $scopeConfig;
        $this->_httpRequest = $httpRequest;
    }

    public function getWeightUnit()
    {
        return $this->_scopeConfig->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function checkComparePage(){
        if($this->_httpRequest->getControllerName() != null) {
            if (strpos($this->_httpRequest->getControllerName(), "product_compare") !== false) {
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }
}
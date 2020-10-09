<?php
/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Customer
 *
 * Date: April, 24 2020
 * Time: 2:45 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

namespace SM\Customer\Model\Metadata;

class Form extends \Magento\Customer\Model\Metadata\Form
{
    /**
     * @override
     * @param \Magento\Framework\App\RequestInterface $request
     * @param null                                    $scope
     * @param bool                                    $scopeOnly
     *
     * @return array
     */
    public function extractData(\Magento\Framework\App\RequestInterface $request, $scope = null, $scopeOnly = true)
    {
        $data = [];
        $params = $request->getParams();
        foreach ($this->getAllowedAttributes() as $attribute) {
            if (!isset($params[$attribute->getAttributeCode()]) &&
                !(isset($_FILES[$attribute->getAttributeCode()])) &&
                isset($this->_attributeValues[$attribute->getAttributeCode()])
            ) {
                $data[$attribute->getAttributeCode()] = $this->_attributeValues[$attribute->getAttributeCode()];
                continue;
            }

            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setRequestScope($scope);
            $dataModel->setRequestScopeOnly($scopeOnly);
            $data[$attribute->getAttributeCode()] = $dataModel->extractValue($request);
        }

        return $data;
    }
}

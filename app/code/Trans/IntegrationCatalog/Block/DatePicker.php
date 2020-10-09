<?php
 
/**
 * @category Trans
 * @package  Trans_IntegrationCatalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author  Ilma Dinnia Alghani <ilma.dinnia@transdigital.co.id>
 *
 * Copyright © 2020 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */


namespace Trans\IntegrationCatalog\Block;
 
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
 
class DatePicker extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var  Registry
     */
    protected $_coreRegistry;
 
    /**
     * @param Context  $context
     * @param Registry $coreRegistry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
 
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        //get configuration element
        $html = $element->getElementHtml();
        //check datepicker set or not
        if (!$this->_coreRegistry->registry('datepicker_loaded')) {
            $this->_coreRegistry->registry('datepicker_loaded', 1);
        }
        //add icon on datepicker 
        $html .= '<button type="button" style="display:none;" class="ui-datepicker-trigger '
            .'v-middle"><span>Select Date</span></button>';
        // add datepicker with element by jquery
        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/ui"], function (jq) {
                jq(document).ready(function () {
                    jq("#' . $element->getHtmlId() . '").datepicker( { 
                        dateFormat: "yy-mm-dd",
                        changeYear: "true",
                        changeMonth: "true",
                        showButtonPanel: "true"
                        });
                    jq(".ui-datepicker-trigger").click(function(){
                        jq("#' . $element->getHtmlId() . '").focus();
                    });
                });
            });
            </script>';
        // return datepicker element
        return $html;
    }
}

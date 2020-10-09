<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   J.P <jaka.pondan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Core\Helper;


/**
 * Class Data
 */
class Exceptions extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     *
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
    }

    /**
     * Generate payload
     * @param array $key (string), $arrray 
     * @return bool
     */
    public function add($code=200,$msg=""){
        $e = "";
        switch ($code) {
            case 400:
                $e = \Magento\Framework\Webapi\Exception::HTTP_BAD_REQUEST;
                break;
            case 406:
                $e = \Magento\Framework\Webapi\Exception::HTTP_NOT_ACCEPTABLE;
                break;
            case 500:
                $e = \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR;
                break;
           
        }
        if(empty($e)){
            return false;
        }
        throw new \Magento\Framework\Webapi\Exception(
            __(
                $msg
            ), 0, $e);
    }
}
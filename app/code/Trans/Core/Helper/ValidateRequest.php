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

use \Magento\Framework\App\Helper\Context;
use \Trans\Core\Helper\Exceptions;

/**
 * Class Data
 */
class ValidateRequest extends \Magento\Framework\App\Helper\AbstractHelper
{
     /**
	 * Error items
	 */
    const ERR_KEY_EMPTY = "Theres no require field";
    const ERR_BODY_EMPTY = "Request body are required";
    const ERR_KEY_REQ_ONE = "Field %s are required";
    const ERR_KEY_REQ = "Field %s are required at row data %d";

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     *
     */
    public function __construct(
        Context $context,
        Exceptions $exception
    )
    {
        parent::__construct($context);
        $this->exception = $exception;
    }

    /**
     * Generate payload
     * @param array $key (string), $arrray 
     * @return bool
     */
    public function one($key="", $array=[]){
        if(empty($key)){
            $this->exception->add(406,self::ERR_KEY_EMPTY);
        }
        $emptyArray = array_filter( $array);
        if(empty($array)){
            $this->exception->add(406,self::ERR_BODY_EMPTY);
        }
        if(!isset($array[$key])){
            $this->exception->add(400,sprintf(self::ERR_KEY_REQ_ONE,$key));
        }
    }

    /**
     * Generate payload
     * @param array $key (array) , $arrray
     * @return bool
     */
    public function many($key=[], $array=[]) {

        $emptyArray = array_filter( $array);
        $emptykey = array_filter( $key);
        if(empty($emptykey)){
            $this->exception->add(406,self::ERR_KEY_EMPTY);
        }
        if(empty($emptyArray) ){
            $this->exception->add(406,self::ERR_BODY_EMPTY);
        }
        $no=1;
        foreach ($array as $nested) {
            if (is_array($nested) ){
                foreach($key as $niddle){
                    if (!array_key_exists($niddle, $nested)){
                        $this->exception->add(400,sprintf(self::ERR_KEY_REQ,$niddle,$no));
                        break;
                    }
                }
            } 
            $no++;
        }
      

    }


}
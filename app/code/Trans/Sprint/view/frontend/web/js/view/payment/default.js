/**
 * @category Trans
 * @package  Trans_Sprint
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

define([
        'Trans_Sprint/js/action/set-service-fee'
    ], function(setServiceFee){
    'use strict';    
    
    return function(targetModule){
        
        return targetModule.extend({
            
            selectPaymentMethod:function()
            {
                var result = this._super(); //call parent method
                setServiceFee(0);
                return result;
            }
        });
    };
});
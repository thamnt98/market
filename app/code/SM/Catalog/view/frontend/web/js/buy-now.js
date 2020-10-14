/**
 * @category  SM
 * @package   SM_Catalog
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author    Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright 2020 Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

define([
    'jquery',
    'mage/translate',
], function ($, $t) {
    "use strict";
    return function (config) {
        var buyNow = $('#product-buy-now'),
            addToCart = $('#product-addtocart-button');
        buyNow.click(function () {
            var form = $(config.form),
                baseUrl = form.attr('action'),
                addToCartUrl = 'checkout/cart/add',
                buyNowCartUrl = 'catalog/cart/add',
                buyNowUrl = baseUrl.replace(addToCartUrl, buyNowCartUrl);
            form.attr('action', buyNowUrl);
            form.trigger('submit');
            form.attr('action', baseUrl);

            addToCart.attr('disabled', true);
            addToCart.removeClass('disabled');
            addToCart.find('span').text($t('Add to Cart'));
            return false;
        });
    }
});

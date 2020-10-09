/**
 * SMCommerce
 *
 * @category    SM
 * @package     SM_GroupProduct
 *
 * Date: May, 14 2020
 * Time: 2:54 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */

define(
    [
        'jquery',
        'mage/translate',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, $t, priceUtils) {
        "use strict";

        $(document).ready(function () {
            let $totalPrice = $('#group-total-price .total-price'),
                $qty        = $('input.item-input-qty');

            $('.decrease-qty').click(function () {
                let input = $(this).parent().find('input'),
                    qty   = parseInt($(input).val());

                if (qty < 2) {
                    let productId = $(input).data('item-id'),
                        $options  = $('.swatch-opt[data-option-id=' + productId + ']');

                    $(input).val(0);
                    $options.find('input').prop('disabled', true);
                } else {
                    $(input).val(qty - 1);
                }

                $(input).trigger('change');
            });

            $('.increase-qty').click(function () {
                let input     = $(this).parent().find('input'),
                    qty       = parseInt($(input).val()),
                    productId = $(input).data('item-id'),
                    $options  = $('.swatch-opt[data-option-id=' + productId + ']');

                $options.find('input').prop('disabled', false);
                $(input).val(qty + 1);
                $(input).trigger('change');
            });

            $($qty).change(function () {
                updateTotalPrice();
            });

            function updateTotalPrice()
            {
                let total = 0;

                $($qty).each(function (key, element) {
                    let qty       = parseInt($(element).val()),
                        itemPrice = $(element).data('item-price');

                    total = total + qty * itemPrice;
                });

                $($totalPrice).text(priceUtils.formatPrice(total));
            }
        });
    }
);
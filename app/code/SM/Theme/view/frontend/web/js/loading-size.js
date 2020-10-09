/**
 * SMCommerce
 *
 * @category  SM
 * @package   SM_Theme
 *
 * Date: May, 02 2020
 * Time: 2:43 PM
 * User: VooThanh DEV
 *
 * @author    SMCommerce Core Team <connect@smartosc.com>
 * @copyright Copyright SMCommerce (http://smartosc.com/)
 */
// todo
require([
    'jquery'
], function ($) {
    let content = $('body').html(),
        time = 0;
    updateLoading();


    function updateLoading()
    {
        setTimeout(function () {
            let temp = $('body').html();

            if (content !== temp) {
                time += 0.5;
                content = temp;
                updateLoading();
            } else {
                $('.loading-modal').hide();
            }
        }, 500);

    }

});

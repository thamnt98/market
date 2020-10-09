define(
    [
        'jquery',
        'JsSocials',
        'mage/translate'
    ],
    function ($, jsSocials) {
        "use strict";

        return function (config) {
            $("#social-btn-" + config.list_id).jsSocials({
                url: config.share_url,
                showLabel: false,
                showCount: false,
                shareIn: "popup",
                shares: ["messenger", "facebook", "googleplus", "pinterest", "twitter"]
            });
        }
    }
);


define([
    'jquery',
    'jquery/ui'
], function ($) {
    return function (widget) {
        $.widget('custom.ticker', widget, {
            options: {
                secondsInDay: 86400,
                secondsInHour: 3600,
                secondsInMinute: 60,
                msInSecond: 1000
            },

            /**
             * @private
             */
            _create: function () {
                var interval;

                interval = setInterval($.proxy(function () {
                    var seconds = this._getEstimate(),
                        daySec = Math.floor(seconds / this.options.secondsInDay) * this.options.secondsInDay,
                        hourSec = Math.floor(seconds / this.options.secondsInHour) * this.options.secondsInHour,
                        minuteSec =  Math.floor(seconds / this.options.secondsInMinute) * this.options.secondsInMinute;

                    if(seconds > 0) {
                        if (typeof dataLayerSourceObjects !== "undefined") {
                            let data = [];
                            data['hour'] = this._formatNumber(Math.floor((hourSec) / this.options.secondsInHour));
                            data['minute'] = this._formatNumber(Math.floor((minuteSec - hourSec) / this.options.secondsInMinute));
                            data['second'] = this._formatNumber(seconds - minuteSec);
                            dataLayerSourceObjects['promoTime'] = data['hour'] + ':' + data['minute'] + ':' + data['second'];
                        }
                        $(".row-surpriseDeals").prop("disabled", false);

                        this.element.find('[data-container="days"]').html(
                            this._formatNumber(Math.floor(daySec / this.options.secondsInDay))
                        );
                        this.element.find('[data-container="hour"]').html(
                            this._formatNumber(Math.floor((hourSec) / this.options.secondsInHour))
                        );
                        this.element.find('[data-container="minute"]').html(
                            this._formatNumber(Math.floor((minuteSec - hourSec) / this.options.secondsInMinute))
                        );
                        this.element.find('[data-container="second"]').html(this._formatNumber(seconds - minuteSec));

                        this.element.find('[data-container="days"]').parent().css('display', 'none');
                        this.element.find('[data-container="hour"]').parent().css('display', 'inline-block');
                        this.element.find('[data-container="minute"]').parent().css('display', 'inline-block');
                        this.element.find('[data-container="second"]').parent().css('display', 'inline-block');

                        if (!seconds) {
                            clearInterval(interval);
                        }
                    }else{
                        $(".row-surpriseDeals").prop("disabled", true);
                        $(".row-surpriseDeals").css("display","none");
                    }
                }, this), this.options.msInSecond);
            },

            /**
             * Get estimated remaining seconds
             *
             * @returns {Number}
             * @private
             */
            _getEstimate: function () {
                var userDate = new Date(),
                    userDateOffset = userDate.getTimezoneOffset(),
                    userDateOffsetMsc = userDateOffset * this.options.secondsInMinute * this.options.msInSecond,
                    userDateUtc = new Date(userDate.getTime() + userDateOffsetMsc),
                    endDate = new Date(this.options.eventEndTimeUTC * this.options.msInSecond + userDateOffsetMsc),
                    result = (endDate.getTime() - userDateUtc.getTime())  / this.options.msInSecond;

                return result < 0 ? 0 : Math.round(result);
            },

            /**
             * Format number, prepend 0 for single digit number
             *
             * @param {Number} number
             * @returns {String}
             * @private
             */
            _formatNumber: function (number) {
                return number < 10 ? '0' + number.toString() : number.toString();
            }
        });
        return $.custom.modal;
    };
});

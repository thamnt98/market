/**
 * @category SM
 * @package SM_Coachmarks
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * @author Logan Ta <phuongtt2@smartosc.com>
 *
 * @copyright   Copyright (c) SmartOSC. All rights reserved.
 * @url http://www.smartosc.com/
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'mage/url',
    'SM_Coachmarks/js/view/maincoachmarks-flag',
    'mage/translate'
], function ($, Component, confirmation, urlBuilder, maincoachmarks) {
    'use strict';

    return Component.extend({

    /**
         * init function
         */
        initialize: function (config) {
            var self = this;
            //limit monitor width to work
            if ($(window).width() >= 767) {
                const header = $('.page-header'),
                    headerStaticHeight = header.outerHeight() + 30;
                //keep height body content when scroll
                $(window).scroll(function () {
                    if ($(window).scrollTop() > 0 && (header.hasClass('sticky'))) {
                        $('body').css('margin-top', headerStaticHeight);
                    } else {
                        $('body').css('margin-top', '0px');
                    }
                });
             }

                var topic = config.topic,
                    topicId = '';
                //filter topic Id current allow running
                $.each(topic, function (i, topicCollect) {
                    $.each(topicCollect.items, function (topicKey, topicVal) {
                        topicId = topicVal.topic_id;
                    });
                });
                //call request action
                $.ajax(
                    {
                        url: urlBuilder.build("coachmarks/customer/allowaction"),
                        type: "post",
                        dataType: "json",
                        data: {topicId: topicId},
                        showLoader: true,
                        success: function (result) {
                            if (!$.isEmptyObject(result)) {
                                var response = result.response;
                                /**get result response**/
                                if (response) {
                                    self.__canAction(config)
                                }
                            }
                        }
                    }
                );

        },

        /**
         * filter tooltip can find element real
         */
        __filterTooltip: function (tooltip) {
            //filter element target tooltip can action
            $.each(tooltip, function (i, tooltipCollect) {
                $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                    if (tooltipVal.tooltip_id !== '') {
                        if(tooltipVal.find_type === 'CLASS' && $(document).find("." + tooltipVal.class_element_html).length === 0){
                            tooltipCollect.items[tooltipKey] = null;
                        }

                        if(tooltipVal.find_type === 'ID' && $(document).find("#" + tooltipVal.id_element_html).length === 0){
                            tooltipCollect.items[tooltipKey] = null;
                        }
                    }
                });
            });

            return tooltip;
        },

        /**
         * __can Action
         */
        __canAction: function (config) {
            var self = this;
            var topic = config.topic,
                tooltip = '',
                topicId = '',
                toolTipActive = false,
                containerBody = $(document).find("[data-container=body]"),
                elTooltipActive = $("[toolTipActive=true]"),
                pageWrapper = $('.page-wrapper'),
                finishTooltip = false;

            var screenWidth = $(window).width(),
                screenMobile = 1023  ;

            /*filter tooltip can find element real*/
            tooltip = self.__filterTooltip(config.tooltip);
            /*run tooltip when page loaded*/
            if (containerBody && topic && tooltip) {
                $(document).ready(function () {
                    /**load topic available for page and make the first tooltip item of first topic is item active**/
                    self.__getTooltipAvailable(topic, tooltip, topicId, toolTipActive);
                    /**set position and fill value for tooltip active**/
                    if ($("[toolTipActive=true]").length > 0) {
                        /**add body modal marks**/
                        containerBody.append('<div class="coachmarks-modals-overlay" id="coachmarks-modals-overlay"></div>');
                        pageWrapper.after(self.templateTooltipDefault());

                        self.setPositionForTooltipActive(tooltip);
                        self.fillDataTopicTooltipActive(tooltip, toolTipActive = true, -1);
                        self.fillDataTooltipStepTopicActive(topic, tooltip, toolTipActive = true);
                    }
                    /**check first step to custom template show tooltip**/
                    var isSingleTooltipActive = self.__isSingleTooltipActive(tooltip),
                        stepFirstNext = 0;
                    if (isSingleTooltipActive) {
                        //close tooltip
                        self.__asFinishTooltipTemplate(stepFirstNext, false);
                        finishTooltip = true;
                    }
                });

                /**next step**/
                $('body').on('click', '#tooltip-bt-next', function (event) {

                    var currentTopicIdRunning = $("[toolTipActive=true]").attr('data-topicid'),
                        currentTooltipIdRunning = $("[toolTipActive=true]").attr('data-tooltipid'),
                        currentStepRunning = $("[toolTipActive=true]").attr('data-tooltip-step'),
                        stepNext = parseInt(currentStepRunning) + 1;
                    //push event GTM
                    //self.__actionPushGTM(0, stepNext, "coachmark_click");
                   // self.__actionPushGTM(0, stepNext + 1, "coachmark_load");

                    var tooltipStepEnd = false,
                        currentTooltipIdNext = $('[data-tooltip-step=' + stepNext + ']'),
                        dataTopicIdTooltipNext = $('[data-tooltip-step=' + stepNext + ']').attr('data-topicid');

                    var elTooltipActiveNext = $("[toolTipActive=true]"),
                        containerBody = $(document).find("[data-container=body]"),
                        templateTooltipDefault = $('#coach-marks-topic');

                    templateTooltipDefault.attr("class",'coach-marks-topic coach-marks-topic--'+stepNext);
                    /** can not find tooltip next step*/
                    if (currentTooltipIdNext.length === 0 && dataTopicIdTooltipNext === undefined) {
                        //close tooltip
                        self.__asFinishTooltipTemplate(stepNext, false);
                        finishTooltip = true;
                    }

                    if (currentTooltipIdNext.length > 0 && dataTopicIdTooltipNext !== undefined
                        && (parseInt(dataTopicIdTooltipNext) === parseInt(currentTopicIdRunning))) {

                        /**next step tooltip of current topic**/
                        $("[toolTipActive=true]").removeAttr("toolTipActive");
                        currentTooltipIdNext.attr("toolTipActive", true);

                        if(screenWidth <  screenMobile){
                            if(stepNext == 2 && ($('body').hasClass('cms-home'))){
                                $('html').addClass('nav-before-open nav-open');
                            }
                        }

                        //set position and fill value for tooltip active
                        if (elTooltipActiveNext) {
                            self.setPositionForTooltipActive(tooltip);
                            self.fillDataTopicTooltipActive(tooltip, toolTipActive = true, currentTooltipIdRunning);
                            //close tooltip
                            self.__asFinishTooltipTemplate(stepNext, false);
                            finishTooltip = true;
                        }
                        //end tooltip step end
                        tooltipStepEnd = true;

                    } else {
                        /**next tooltip of next topic**/
                        var currentTopicIdRunning = $("[toolTipActive=true]").attr('data-topicid'),
                            currentTooltipIdRunning = $("[toolTipActive=true]").attr('data-tooltipid'),
                            currentTopicStepRunning = $("[toolTipActive=true]").attr('data-step-topic'),
                            stepTopicNext = parseInt(currentTopicStepRunning) + 1,
                            topicStepEnd = false,
                            topicStepIdNext = $('[data-step-topic=' + stepTopicNext + ']'),
                            elTooltipActiveNext = $("[toolTipActive=true]");
                        $("[toolTipActive=true]").removeAttr("toolTipActive");
                        topicStepIdNext.attr("toolTipActive", true);

                        //set position and fill value for tooltip active
                        if (elTooltipActiveNext && topicStepIdNext.length > 0) {
                            self.setPositionForTooltipActive(tooltip);
                            self.fillDataTopicTooltipActive(tooltip, toolTipActive = true, currentTooltipIdRunning);
                            //close tooltip
                            self.__asFinishTooltipTemplate(false, stepTopicNext);
                            finishTooltip = true;
                        }
                    }
                });

                /**finish**/
                //case tooltip position top
                $('.coach-marks-topic').on('click', '#tooltip-bt-finish', function (event) {
                    event.preventDefault();
                    self.__closeTooltip();
                    //push data to GTM
                    var currentStepRunning = $("[toolTipActive=true]").attr('data-tooltip-step'),
                        stepNext = parseInt(currentStepRunning) + 1;
                    self.__actionPushGTM(0, stepNext, 'coachmark_click');
                    if( $('.nav-before-open').length > 0){
                        $('html').removeClass('nav-before-open nav-open');
                    }

                });
                //case tooltip position bottom
               /* $('.tooltip-bottom').on('click', '#tooltip-bt-finish', function (event) {
                    event.preventDefault();
                    self.__closeTooltip();
                    //push data to GTM
                    var currentStepRunning = $("[toolTipActive=true]").attr('data-tooltip-step'),
                        stepNext = parseInt(currentStepRunning) + 1;
                    self.__actionPushGTM(0, stepNext, "coachmark_click");
                });*/
            }
        },

        /**
         * as finish tooltip template
         */
        __asFinishTooltipTemplate: function (stepTooltipNext, stepTopicNext) {
            console.log(stepTopicNext + " " +stepTooltipNext );
            //case step topic
            if (stepTopicNext && $('[data-step-topic=' + parseInt(stepTopicNext + 1) + ']').length < 1) {
                $('#tooltip-bt-next').hide();
                $('#tooltip-bt-finish').show();
                $('#tooltip-bt-finish').html($.mage.__('Finish'));
                $('#coach-marks').removeClass('coach-marks-skip');
            }

            //case step tooltip
            if (stepTooltipNext !== '' && $('[data-tooltip-step=' + parseInt(stepTooltipNext + 1) + ']').length < 1) {
                $('#tooltip-bt-next').hide();
                $('#tooltip-bt-finish').show();
                $('#tooltip-bt-finish').html($.mage.__('Finish'));
                $('#coach-marks').removeClass('coach-marks-skip');
            }
        },

        /**
         * close to end tooltip
         */
        __closeTooltip: function () {
            var templateTooltipDefault = $('#coach-marks-topic'),
                topicId = $("[toolTipActive=true]").attr('data-topicid');
            if(topicId === undefined){
                topicId = $(".tooltip").attr('data-topicid');
            }
            maincoachmarks.coachMarks(true);
            $.ajax(
                {
                    url: urlBuilder.build("coachmarks/customer/saveattrcoachmark"),
                    type: "post",
                    dataType: "json",
                    data: {topicId: topicId},
                    showLoader: true,
                    success: function (result) {
                        if (!$.isEmptyObject(result)) {
                            var response = result.response;
                            /**remove attr html and modals overlay**/
                            if (response) {
                                $("[toolTipActive=true]").removeAttr("toolTipActive");
                                $('#coachmarks-modals-overlay').hide();
                                templateTooltipDefault.hide();
                            }
                        }
                    }
                }
            );
        },

        /**
         * set Position For Tooltip Active
         */
        setPositionForTooltipActive: function (tooltip) {
            var screenWidth = $(window).width(),
                screenMobile = 1023  ;

            var tooltipPosition = $("[toolTipActive=true]").offset(),
                coachMarksTopic = $('div.coach-marks-topic'),
                divTooltipPos = $("[toolTipActive=true]"),
                widthToolTip = 400,
                divTooltipTop = coachMarksTopic.find('.tooltip'),
                divTooltipBottom = coachMarksTopic.find('.tooltip-bottom'),
                tooltipTopPoint = coachMarksTopic.find('.tooltip-top-pointer'),
                tooltipBottomPoint = coachMarksTopic.find('.tooltip-bottom-pointer');

            if (tooltipPosition.top < 150) {

                var top = parseInt(tooltipPosition.top) + parseInt($("[toolTipActive=true]").outerHeight());

                //case show tooltip bottom position
                if (divTooltipTop.length > 0) {
                    divTooltipTop.addClass('tooltip-bottom');
                    divTooltipTop.removeClass('tooltip');
                    tooltipTopPoint.hide();
                    tooltipBottomPoint.show();
                }
                // control pointer for bottom position
                // end control pointer for top position
                coachMarksTopic.css('top', top);

                // Based on screen size
                if(screenWidth > screenMobile ){
                    if(parseInt(tooltipPosition.left + divTooltipPos.width() + widthToolTip) >= parseInt($(window).width())){
                        coachMarksTopic.css('left', parseInt(screenWidth - divTooltipPos.width()/2  - widthToolTip ));

                    }else if(tooltipPosition.left < 20 ){
                        coachMarksTopic.css('left', tooltipPosition.left+ 20);
                    }else{
                        coachMarksTopic.css('left', tooltipPosition.left);
                    }
                }else{
                    if(parseInt(tooltipPosition.left + widthToolTip) > parseInt($(window).width())){
                        if((screenWidth - widthToolTip) > 10){
                            coachMarksTopic.css('left', parseInt(screenWidth - widthToolTip ));
                        }else{
                            coachMarksTopic.css('left', 10);
                        }
                    }else if(tooltipPosition.left < 10 ){
                        coachMarksTopic.css('left', 10);
                    }else{
                        coachMarksTopic.css('left', tooltipPosition.left);
                    }

                }


            } else {

                //case show tooltip top position
                if (divTooltipBottom.length > 0) {
                    divTooltipBottom.addClass('tooltip');
                    divTooltipBottom.removeClass('tooltip-bottom');
                    tooltipBottomPoint.hide();
                    tooltipTopPoint.show();
                }
                // control pointer for top positio
                // end control pointer for top position
                coachMarksTopic.css('top', tooltipPosition.top);

                // Based on screen size
                if(screenWidth > screenMobile ) {
                    if (parseInt(tooltipPosition.left + divTooltipPos.width() + widthToolTip) >= parseInt($(window).width())) {
                        coachMarksTopic.css('left', parseInt(screenWidth - divTooltipPos.width()/2  - widthToolTip ));
                    } else if (tooltipPosition.left < 20) {
                        coachMarksTopic.css('left', tooltipPosition.left + 20);
                    } else {
                        coachMarksTopic.css('left', tooltipPosition.left);
                    }
                }else{

                    if(parseInt(tooltipPosition.left + widthToolTip) > parseInt($(window).width())){
                        if((screenWidth - widthToolTip) > 10){
                            coachMarksTopic.css('left', parseInt(screenWidth - widthToolTip ));
                        }else{
                            coachMarksTopic.css('left', 10);
                        }
                        //coachMarksTopic.css('left', parseInt(tooltipPosition.left - widthToolTip / 2));
                    }else if(tooltipPosition.left < 10 ){
                        coachMarksTopic.css('left', 10);
                    }else{
                        coachMarksTopic.css('left', tooltipPosition.left);
                    }
                }

            }
        },

        /**
         * template Tooltip Default
         */
        templateTooltipDefault: function () {
            var html = '';
            html = '<div class="coach-marks coach-marks-skip" id="coach-marks">' +
                '<div class="coach-marks-topic " id="coach-marks-topic">' +
                '        <div class="tooltip">' +
                '            <div id="tooltip_content">' +
                '                <div id="tool-content"></div>' +
                '                <div id="tool-next"><button type="button" title="Next" class="action submit primary" id="tooltip-bt-next">' + $.mage.__('Next') + '</button></div>' +
                '                <div id="tool-finish"><button type="button" title="Finish" class="action submit primary" id="tooltip-bt-finish">' + $.mage.__('Skip') + '</button></div>' +
                '                <div id="tool-step"></div>' +
                '            </div>' +
                '            <div class="tooltip-top-pointer"></div>' +
                '            <div class="tooltip-bottom-pointer" style="display: none;"></div>' +
                '        </div>' +
                '        </div>' +
                '    </div>';

            return html;
        },

        /**
         * get Tooltips Available
         */
        __getTooltipAvailable: function (topic, tooltip, topicId, toolTipActive) {
            var self = this;
            //load topic available for page
            $.each(topic, function (i, topicCollect) {
                $.each(topicCollect.items, function (topicKey, topicVal) {
                    topicId = topicVal.topic_id;
                    $.each(tooltip, function (i, tooltipCollect) {
                        $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                            if (tooltipVal !== null && tooltipVal.topic_id === topicId) {
                                if (!toolTipActive) {
                                    if(tooltipVal.find_type === 'CLASS'){
                                        $('.' + tooltipVal.class_element_html).attr("toolTipActive", true);
                                        self.actionFillTooltipAttributeHTML(topicKey, tooltipKey, tooltipVal);
                                        toolTipActive = true;
                                    }else{
                                        $('#' + tooltipVal.id_element_html).attr("toolTipActive", true);
                                        self.actionFillTooltipAttributeHTML(topicKey, tooltipKey, tooltipVal);
                                        toolTipActive = true;
                                    }
                                } else {
                                    self.actionFillTooltipAttributeHTML(topicKey, tooltipKey, tooltipVal);
                                }
                            }
                        });
                    });
                });
            });
        },

        /**
         * action Fill ToolTip Attribute Element HTML
         */
        actionFillTooltipAttributeHTML: function (topicKey, tooltipKey, tooltipVal) {
            if(tooltipVal.find_type === 'CLASS'){
                //case find element setup tooltip tyle class
                $('.' + tooltipVal.class_element_html).attr("data-topicId", tooltipVal.topic_id);
                $('.' + tooltipVal.class_element_html).attr("data-step-topic", topicKey);
                $('.' + tooltipVal.class_element_html).attr("data-tooltipId", tooltipVal.tooltip_id);
                $('.' + tooltipVal.class_element_html).attr("data-tooltip-step", tooltipKey);
                $('.' + tooltipVal.class_element_html).attr("data-sortOrder", tooltipVal.sort_order);
                //check attribute class exist
                if ($('.' + tooltipVal.class_element_html).attr("class") !== '') {
                    $('.' + tooltipVal.class_element_html).addClass('tooltip');
                } else {
                    $('.' + tooltipVal.class_element_html).attr("class", 'tooltip');
                }
                $('.' + tooltipVal.class_element_html).attr("data-tooltip-content", '#tooltip_content');
            }else{
                //case find element setup tooltip tyle id
                $('#' + tooltipVal.id_element_html).attr("data-topicId", tooltipVal.topic_id);
                $('#' + tooltipVal.id_element_html).attr("data-step-topic", topicKey);
                $('#' + tooltipVal.id_element_html).attr("data-tooltipId", tooltipVal.tooltip_id);
                $('#' + tooltipVal.id_element_html).attr("data-tooltip-step", tooltipKey);
                $('#' + tooltipVal.id_element_html).attr("data-sortOrder", tooltipVal.sort_order);
                //check attribute class exist
                if ($('#' + tooltipVal.id_element_html).attr("class") !== '') {
                    $('#' + tooltipVal.id_element_html).addClass('tooltip');
                } else {
                    $('#' + tooltipVal.id_element_html).attr("class", 'tooltip');
                }
                $('#' + tooltipVal.id_element_html).attr("data-tooltip-content", '#tooltip_content');
            }
        },

        /**
         * fill Data Topic Tooltip Active
         */
        fillDataTopicTooltipActive: function (tooltip, toolTipActive, currentTooltipIdRunning) {
            var self = this;
            var tooltipActiveId = $("[toolTipActive=true]").attr('data-tooltipId'),
                tooltipActiveStep = $("[toolTipActive=true]").attr('data-tooltip-step');
            //load topic available for page
            $.each(tooltip, function (i, tooltipCollect) {
                $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                    if (tooltipVal !== null && tooltipVal.tooltip_id === tooltipActiveId && toolTipActive === true) {
                        self.__actionFillDataTooltipActive(tooltipKey, tooltipVal.content, tooltipVal.sort_order);
                    }
                });
            });

            //make high light for the active note
            if (currentTooltipIdRunning !== -1) {
                $('span.tooltip_step_' + parseInt(currentTooltipIdRunning - 1)).removeClass('make-note-highligh');
            }
            $('span.tooltip_step_' + parseInt(tooltipActiveStep - 1)).removeClass('make-note-highligh');
            $('span.tooltip_step_' + tooltipActiveStep).addClass('make-note-highligh');
        },

        /**
         * action Fill Data ToolTip Active
         */
        __actionFillDataTooltipActive: function (tooltipKey, tooltipValContent, tooltipValSortOrder) {
            var toolTipContent = $('#tool-content');

            toolTipContent.empty();
            toolTipContent.append(tooltipValContent);
        },

        /**
         * fill Data Tooltip Step Topic Active
         */
        fillDataTooltipStepTopicActive: function (topic, tooltip, toolTipActive) {
            var self = this;
            var html = '',
                toolTipStep = $('#tool-step'),
                topicId = '',
                firstStep = true,
                stepNext = false;

            $.each(topic, function (i, topicCollect) {
                $.each(topicCollect.items, function (topicKey, topicVal) {
                    topicId = topicVal.topic_id;
                    $.each(tooltip, function (i, tooltipCollect) {
                        $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                            if (tooltipVal !== null && tooltipVal.topic_id === topicId && toolTipActive) {
                                if (firstStep) {
                                    //make high light for the first tooltip active node
                                    html += '<span class="tooltip_step_' + tooltipKey + ' make-note-highligh">&nbsp;</span>';
                                    firstStep = false;
                                    self.__actionPushGTM(1, stepNext, "coachmark_load");
                                } else {
                                    html += '<span class="tooltip_step_' + tooltipKey + '">&nbsp;</span>';
                                }
                            }
                        });
                    });
                });
            });

            toolTipStep.prepend(html);
        },

        /**
         * __is Single Tooltip Active
         */
        __isSingleTooltipActive: function (tooltip) {
            var isSingle = false,
                countTooltip = 0;
            //load tooltip available for page
            $.each(tooltip, function (i, tooltipCollect) {
                $.each(tooltipCollect.items, function (tooltipKey, tooltipVal) {
                    if (tooltipVal !== null && tooltipVal.tooltip_id !== '') {
                        countTooltip = countTooltip + 1;
                    }
                });
            });

            if (countTooltip > 1) {
                return false;
            }

            return true;
        },

        /**
         * action push data google task manager
         */
        __actionPushGTM: function (coachmark_step, stepNext, event) {
            /*if (typeof dataLayerSourceObjects !== 'undefined' && dataLayerSourceObjects.customer.loginType !== "null") {
                var step = "Step 1";
                window.dataLayer = window.dataLayer || [];
                if(coachmark_step !== 1){
                    step = "Step " + stepNext;
                }
                window.dataLayer.push({
                    "event": event,
                    'uniqueUserID': dataLayerSourceObjects.customer.uniqueUserID,
                    'userID': dataLayerSourceObjects.customer.userID,
                    'customerID': dataLayerSourceObjects.customer.customerID,
                    'customerType': dataLayerSourceObjects.customer.customerType,
                    'loyalty': dataLayerSourceObjects.customer.loyalty,
                    'customerStatus': dataLayerSourceObjects.customer.customerStatus,
                    'loginType': dataLayerSourceObjects.customer.loginType,
                    'store_name': dataLayerSourceObjects.customer.storeName,
                    'store_ID': dataLayerSourceObjects.customer.storeID,
                    "coachmark_step": step
                });
            }*/
        },

    })
});

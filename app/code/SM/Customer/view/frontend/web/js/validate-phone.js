define(
    [
        'jquery',
        'mage/url',
        'mage/validation',
        'mage/translate'
    ],
    function ($, urlBuilder) {
        let mod = {};
        var telephoneErrorSelector;
        mod.create = function (formSelector) {
            var emailAddress = formSelector.find("input[name='email']"),
                telephone = formSelector.find("input[name='telephone']"),
                buttonSubmit = formSelector.find("button[type='submit']");
            telephone.on("change", function () {
                var validateTelephone = $.validator.validateSingleElement($(this));
                if (!validateTelephone) {
                    if (telephoneErrorSelector) {
                        telephoneErrorSelector.hide();
                    }
                    return false;
                }
                var url = urlBuilder.build("rest/V1/uniquephone"),
                    phone = $(this).val();
                buttonSubmit.attr("disabled", "disabled");
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "json",
                    data: JSON.stringify({telephone: phone}),
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.setRequestHeader('Content-Type', 'application/json');
                    },
                    success: function (result) {
                        if (result === true) { // Do this if an phone number not exists
                            telephone.removeClass('validation-failed');
                            if (telephoneErrorSelector) {
                                telephoneErrorSelector.hide();
                            }
                            telephone.attr('exist', 'false');
                            if (emailAddress.attr('exist') == 'false') {
                                buttonSubmit.removeAttr("disabled"); // Enable Register button
                            }
                        } else if (result === false) { // Do this if an phone number is already exists
                            var errorHtml = '<div class="validation-advice" id="advice-required-entry-telephone">' + $.mage.__('Your mobile number has already been registered') + '</div>';
                            telephone.addClass('validation-failed');
                            if (!telephoneErrorSelector) {
                                telephoneErrorSelector = $(errorHtml).insertAfter(telephone);
                            }
                            telephoneErrorSelector.show();
                            telephone.attr('exist', 'true');
                            buttonSubmit.attr("disabled", "disabled");
                            $(document).trigger('customer:already-registered-phone');
                        }
                    }
                });
            });
        };
        return mod;
    }
);

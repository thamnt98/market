define(
    [
        'jquery',
        'mage/url',
        'mage/validation',
        'mage/translate'
    ],
    function ($, urlBuilder) {
        let mod = {};
        var emailErrorSelector;
        mod.create = function (formSelector) {
            var emailAddress = formSelector.find("input[name='email']"),
                telephone = formSelector.find("input[name='telephone']"),
                buttonSubmit = formSelector.find("button[type='submit']");
            emailAddress.on("change", function () {
                var validateEmail = $.validator.validateSingleElement($(this));
                if (!validateEmail) {
                    if (emailErrorSelector) {
                        emailErrorSelector.hide();
                    }
                    return false;
                }
                var url = urlBuilder.build('rest/V1/uniqueemail'),
                    mail = $(this).val(),
                    type = $(this).attr('id');
                // buttonSubmit.attr("disabled", "disabled");
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: "json",
                    data: JSON.stringify({email: mail, type: type}),
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('Accept', 'application/json');
                        xhr.setRequestHeader('Content-Type', 'application/json');
                    },
                    success: function (result) {
                        if (result === true) { // Do this if an email not exists
                            emailAddress.removeClass('validation-failed');
                            if (emailErrorSelector) {
                                emailErrorSelector.hide();
                            }
                            emailAddress.attr('exist', 'false');
                            if (telephone.attr('exist') == 'false') {
                                buttonSubmit.removeAttr("disabled"); // Enable Register button
                            }
                        } else if (result === false) { // Do this if an email is already exists
                            var errorHtml = '<div class="validation-advice mage-error" generated="true" id="advice-required-entry-email_address">' + $.mage.__('Your email has already been registered') + '</div>';
                            emailAddress.addClass('validation-failed');
                            if (!emailErrorSelector) {
                                emailErrorSelector = $(errorHtml).insertAfter(emailAddress);
                            }
                            emailAddress.attr('exist', 'true');
                            emailErrorSelector.show();
                            $(document).trigger('customer:already-registered-email');
                        }
                    }
                });
            });
        };
        return mod;
    }
);

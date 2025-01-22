define([
    'jquery',
    'uiRegistry',
    'CSSoft_OrderCheckout/js/scroll-to-error',
    'mage/validation',
    'mage/translate'
],function ($, registry, scrollToError) {
    'use strict';

    var cssoftCheckoutFieldsEnabled = window.checkoutConfig.cssoftCheckoutFieldsEnabled;

    function validateCheckboxes() {
        var checkboxes = $('.cssoft-checkout-fields input[type="checkbox"][aria-required="true"]:not(:checked)');

        checkboxes.each(function () {
            if ($(this).closest('.control').find('.checkbox-error').length) {
                return;
            }

            $(this).closest('.control').append(
                '<div class="field-error checkbox-error">' +
                    '<span>' +
                        $.mage.__('This is a required field.') +
                    '</span>' +
                '</div>'
            );
        });

        checkboxes.off('.cssoft-checkout-fields');
        checkboxes.on('change.cssoft-checkout-fields', function () {
            $(this).closest('.control').find('.checkbox-error').remove();
        });

        return !checkboxes.length;
    }

    return {
        /**
         * Validate checkout fields
         *
         * @returns {Boolean}
         */
        validate: function (hideError) {
            var checkoutProvider,
                result;

            if (!cssoftCheckoutFieldsEnabled || hideError) {
                return true;
            }

            checkoutProvider = registry.get('checkoutProvider');

            if (typeof checkoutProvider.get('cssoftCheckoutFields') === 'undefined') {
                return true;
            }

            checkoutProvider.set('params.invalid', false);

            if (checkoutProvider.get('cssoftCheckoutFields')) {
                checkoutProvider.trigger('cssoftCheckoutFields.data.validate');
                result = validateCheckboxes() && !checkoutProvider.get('params.invalid');

                if (!result) {
                    scrollToError();
                }

                return result;
            }

            return false;
        }
    };
});

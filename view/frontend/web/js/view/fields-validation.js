define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'CSSoft_CheckoutDetails/js/model/fields-validator'
    ],
    function (Component, additionalValidators, fieldsValidator) {
        'use strict';
        additionalValidators.registerValidator(fieldsValidator);
        return Component.extend({});
    }
);

define([
    'mage/utils/wrapper',
    'CSSoft_CheckoutDetails/js/model/checkout-fields-assigner'
], function (wrapper, checkoutFieldsAssigner) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            checkoutFieldsAssigner(paymentData);

            return originalAction(paymentData, messageContainer);
        });
    };
});

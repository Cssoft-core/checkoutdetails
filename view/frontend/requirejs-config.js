var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'CSSoft_CheckoutDetails/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'CSSoft_CheckoutDetails/js/model/set-payment-information-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information-extended': {
                'CSSoft_CheckoutDetails/js/model/set-payment-information-extended-mixin': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'CSSoft_CheckoutDetails/js/mixin/form/element/abstract-mixin': true
            }
        }
    }
};

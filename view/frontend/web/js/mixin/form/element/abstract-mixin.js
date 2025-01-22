define([
    'CSSoft_OrderCheckout/js/model/storage'
], function (storage) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return function (target) {
        if (!checkoutConfig || !checkoutConfig.cssoftCheckoutFieldsEnabled) {
            return target;
        }

        return target.extend({
            /**
             * Calls initObservable of parent class, initializes observable
             * properties of instance.
             *
             * @return {Object} - reference to instance
             */
            initObservable: function () {
                this._super();

                if (this.cssoftCheckoutField) {
                    this.vault = storage('checkout-fields');
                    this.value.subscribe(this.updateFieldState.bind(this));
                    this.value(this.vault.get(this.id, this.value()));
                }

                return this;
            },

            /**
             * Save field state in the vault
             */
            updateFieldState: function () {
                if (this.cssoftCheckoutField) {
                    this.vault.set(this.id, this.value());
                }
            }
        });
    };
});

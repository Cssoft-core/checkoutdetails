# Checkout Fields

## Installation

### For clients

There are several ways to install extension for clients:

 1. If you've bought the product at Magento's Marketplace - use
    [Marketplace installation instructions](https://docs.magento.com/marketplace/user_guide/buyers/install-extension.html)
 2. Otherwise, you have two options:
    - Install the sources directly from [our repository](https://docs.cssoftsolutions.com/m2/extensions/checkout-fields/installation/composer/) - **recommended**
    - Download archive and use [manual installation](https://docs.cssoftsolutions.com/m2/extensions/checkout-fields/installation/manual/)

### For developers

Use this approach if you have access to our private repositories!

```bash
cd <magento_root>
composer config repositories.cssoft composer http://cssoft.github.io/packages/
composer require cssoft/checkout-fields:dev-master --prefer-source
bin/magento module:enable\
    CSSoft_Core\
    CSSoft_Checkout\
    CSSoft_CheckoutDetails
bin/magento setup:upgrade
```

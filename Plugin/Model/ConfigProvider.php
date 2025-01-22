<?php
namespace CSSoft\CheckoutDetails\Plugin\Model;

class ConfigProvider
{
    /**
     * @var \CSSoft\CheckoutDetails\Helper\Data
     */
    protected $helper;
    /**
     * @param \CSSoft\CheckoutDetails\Helper\Data $helper
     */
    public function __construct(
        \CSSoft\CheckoutDetails\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }
    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array $result
     * @return string
     */
    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {
        if ($this->helper->isEnabled()) {
            $result['cssoftCheckoutFieldsEnabled'] = true;
        }
        return $result;
    }
}

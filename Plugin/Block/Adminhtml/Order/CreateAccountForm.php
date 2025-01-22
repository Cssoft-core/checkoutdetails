<?php
namespace CSSoft\CheckoutDetails\Plugin\Block\Adminhtml\Order;

class CreateAccountForm
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
     * @param  \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account $subject
     * @param  string $result
     * @return string
     */
    public function afterToHtml(
        \Magento\Sales\Block\Adminhtml\Order\Create\Form\Account $subject,
        $result
    ) {
        if ($this->helper->isEnabled()) {
            $fieldsFormHtml = $subject->getLayout()->createBlock(
                'CSSoft\CheckoutDetails\Block\Adminhtml\Order\Create\Form',
                'cssoft_checkout_fields_form'
            )->setTemplate('CSSoft_CheckoutDetails::order/create/form.phtml')
            ->setStore($subject->getStore())
            ->toHtml();

            return $result . $fieldsFormHtml;
        }

        return $result;
    }
}

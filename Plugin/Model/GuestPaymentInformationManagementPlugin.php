<?php
namespace CSSoft\CheckoutDetails\Plugin\Model;

class GuestPaymentInformationManagementPlugin extends AbstractPaymentInformationManagementPlugin
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @param \CSSoft\CheckoutDetails\Model\Field\Validator $fieldsValidator
     * @param \CSSoft\CheckoutDetails\Helper\Data $helper
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \CSSoft\CheckoutDetails\Model\Field\ValueFactory $fieldValueFactory
     * @param \CSSoft\CheckoutDetails\Model\FieldFactory $fieldFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        \CSSoft\CheckoutDetails\Model\Field\Validator $fieldsValidator,
        \CSSoft\CheckoutDetails\Helper\Data $helper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \CSSoft\CheckoutDetails\Model\Field\ValueFactory $fieldValueFactory,
        \CSSoft\CheckoutDetails\Model\FieldFactory $fieldFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        parent::__construct(
            $fieldsValidator, $helper, $quoteRepository,
            $fieldValueFactory, $fieldFactory, $storeManager
        );
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * @param \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param string $email
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSavePaymentInformation(
        \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if ($this->helper->isEnabled()) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $this->validateAndSaveFields($quoteIdMask->getQuoteId(), $paymentMethod);
        }
    }
}

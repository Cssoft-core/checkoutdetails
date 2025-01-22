<?php
namespace CSSoft\CheckoutDetails\Plugin\Model;

class AbstractPaymentInformationManagementPlugin
{
    /**
     * @var \CSSoft\CheckoutDetails\Model\Field\Validator
     */
    protected $fieldsValidator;

    /**
     * @var \CSSoft\CheckoutDetails\Helper\Data
     */
    protected $helper;

    /**
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \CSSoft\CheckoutDetails\Model\Field\ValueFactory
     */
    protected $fieldValueFactory;

    /**
     * @var \CSSoft\CheckoutDetails\Model\FieldFactory
     */
    protected $fieldFactory;

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \CSSoft\CheckoutDetails\Model\Field\Validator $fieldsValidator
     * @param \CSSoft\CheckoutDetails\Helper\Data $helper
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \CSSoft\CheckoutDetails\Model\Field\ValueFactory $fieldValueFactory
     * @param \CSSoft\CheckoutDetails\Model\FieldFactory $fieldFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \CSSoft\CheckoutDetails\Model\Field\Validator $fieldsValidator,
        \CSSoft\CheckoutDetails\Helper\Data $helper,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \CSSoft\CheckoutDetails\Model\Field\ValueFactory $fieldValueFactory,
        \CSSoft\CheckoutDetails\Model\FieldFactory $fieldFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->fieldsValidator = $fieldsValidator;
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->fieldValueFactory = $fieldValueFactory;
        $this->fieldFactory = $fieldFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function validateAndSaveFields(
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
    )
    {
        $fields = $paymentMethod->getExtensionAttributes() === null
            ? []
            : $paymentMethod->getExtensionAttributes()->getCssoftCheckoutFields();

        if (!empty($fields)) {
            if ($this->fieldsValidator->isValid($fields)) {
                $quote = $this->quoteRepository->getActive($cartId);
                $quote->setCssoftCheckoutFields($fields);

                $storeId = $this->storeManager->getStore()->getId();
                foreach ($fields as $code => $value) {
                    if (empty($value) && !is_numeric($value)) {
                        continue;
                    }

                    $fieldId = $this->fieldFactory->create()->loadByCode($code)->getId();

                    $fieldValueModel = $this->fieldValueFactory->create()
                        ->loadByQuoteFieldAndStore($quote->getId(), $fieldId, $storeId);

                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $fieldValueModel->setValue($value)->save();
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please fill all required fields before placing the order.')
                );
            }
        }
    }
}

<?php
namespace CSSoft\CheckoutDetails\Plugin\Block\Checkout;

class LayoutProcessor
{
    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Checkout fields collection factory
     * @var \CSSoft\CheckoutDetails\Model\ResourceModel\Field\CollectionFactory
     */
    protected $fieldsCollectionFactory;

    /**
     * Checkout fields helper
     * @var \CSSoft\CheckoutDetails\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \CSSoft\CheckoutDetails\Model\ResourceModel\Field\CollectionFactory $fieldsCollectionFactory
     * @param \CSSoft\CheckoutDetails\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \CSSoft\CheckoutDetails\Model\ResourceModel\Field\CollectionFactory $fieldsCollectionFactory,
        \CSSoft\CheckoutDetails\Helper\Data $helper
    ) {
        $this->storeManager = $storeManager;
        $this->fieldsCollectionFactory = $fieldsCollectionFactory;
        $this->helper = $helper;
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $jsLayout
    ) {
        if(!$this->helper->isEnabled()) {
            return $jsLayout;
        }

        $storeId = $this->storeManager->getStore()->getId();
        $fields = $this->fieldsCollectionFactory->create()
            ->addStoreFilter($storeId)
            ->addIsActiveFilter(1)
            ->addOrder(
                \CSSoft\CheckoutDetails\Api\Data\FieldInterface::SORT_ORDER,
                \Magento\Framework\Data\Collection::SORT_ORDER_ASC
            );

        if ($fields->getSize()) {
            $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['beforeMethods']
                ['children']['cssoft-checkout-fields'] =
                [
                    'component' => 'uiComponent',
                    'config' => [
                        'template' => 'CSSoft_CheckoutDetails/container'
                    ],
                    'visible' => true,
                    'sortOrder' => 0
                ];
        }

        foreach ($fields as $field) {
            $label = $field->getStoreLabel($storeId);

            $validation = [];
            if ($field->getIsRequired() == 1) {
                if ($field->getFrontendInput() == 'multiselect') {
                    $validation['validate-one-required'] = true;
                }
                $validation['required-entry'] = true;
            }

            $options = $this->helper->getFieldOptions($field, $storeId);
            $default = $this->helper->getDefaultValue($field);

            $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']['beforeMethods']
                ['children']['cssoft-checkout-fields']['children'][$field->getAttributeCode()] =
                $this->helper->getFieldComponent($field, $label, $validation, $default, $options, 'checkoutProvider');
        }
        return $jsLayout;
    }
}

<?php
namespace CSSoft\CheckoutDetails\Controller\Adminhtml\Order;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \CSSoft\CheckoutDetails\Model\Field\Validator
     */
    protected $fieldsValidator;

    /**
     * @var \CSSoft\CheckoutDetails\Model\FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var \CSSoft\CheckoutDetails\Model\Field\ValueFactory
     */
    protected $fieldValueFactory;

    /**
     * @var \Magento\CheckoutFields\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \CSSoft\CheckoutDetails\Model\Field\Validator $fieldsValidator
     * @param \CSSoft\CheckoutDetails\Model\FieldFactory $fieldFactory
     * @param \CSSoft\CheckoutDetails\Model\Field\ValueFactory $fieldValueFactory
     * @param \CSSoft\CheckoutDetails\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \CSSoft\CheckoutDetails\Model\Field\Validator $fieldsValidator,
        \CSSoft\CheckoutDetails\Model\FieldFactory $fieldFactory,
        \CSSoft\CheckoutDetails\Model\Field\ValueFactory $fieldValueFactory,
        \CSSoft\CheckoutDetails\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->fieldsValidator = $fieldsValidator;
        $this->fieldFactory = $fieldFactory;
        $this->fieldValueFactory = $fieldValueFactory;
        $this->helper = $helper;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $storeId = $this->getRequest()->getParam('store_id');
        $fields = $this->getRequest()->getParam('cssoft_checkout_field');

        if ($orderId && !empty($fields)) {
            if (!$this->fieldsValidator->isValid($fields)) {
                return $this->resultJsonFactory->create()->setData([
                    'error' => true,
                    'message' => __('Please fill all required fields.')
                ]);
            }

            foreach ($fields as $code => $value) {
                $field = $this->fieldFactory->create()->loadByCode($code);
                $fieldValueModel = $this->fieldValueFactory->create()
                    ->loadByOrderFieldAndStore($orderId, $field->getId(), $storeId);

                if (empty($value) && !is_numeric($value)) {
                    if ($fieldValueModel->getId()) {
                        $fieldValueModel->delete();
                    }

                    continue;
                }

                if (is_array($value)) {
                    $value = implode(',', $value);
                }

                if ($field->getFrontendInput() === 'date') {
                    $value = $this->helper->parseDateString($value)
                        ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
                }

                $fieldValueModel
                    ->setFieldId($field->getId())
                    ->setStoreId($storeId)
                    ->setOrderId($orderId)
                    ->setValue($value)
                    ->save();
            }
        }

        return $this->resultJsonFactory->create()->setData([
            'success' => true
        ]);
    }
}

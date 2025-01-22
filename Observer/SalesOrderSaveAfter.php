<?php
namespace CSSoft\CheckoutDetails\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSaveAfter implements ObserverInterface
{
    /**
     * @var \CSSoft\CheckoutDetails\Model\ResourceModel\Field\Value\CollectionFactory
     */
    protected $fieldValuesCollectionFactory;

    /**
     * @var \CSSoft\CheckoutDetails\Model\ResourceModel\Field\CollectionFactory
     */
    protected $fieldsCollectionFactory;

    /**
     * @var \CSSoft\CheckoutDetails\Model\FieldFactory
     */
    protected $fieldFactory;

    /**
     * @var \Magento\CheckoutFields\Helper\Data
     */
    protected $helper;

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \CSSoft\CheckoutDetails\Model\ResourceModel\Field\Value\CollectionFactory $fieldValuesCollectionFactory
     * @param \CSSoft\CheckoutDetails\Model\ResourceModel\Field\CollectionFactory $fieldsCollectionFactory
     * @param \CSSoft\CheckoutDetails\Model\FieldFactory $fieldFactory
     * @param \Magento\CheckoutFields\Helper\Data $helper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \CSSoft\CheckoutDetails\Model\ResourceModel\Field\Value\CollectionFactory $fieldValuesCollectionFactory,
        \CSSoft\CheckoutDetails\Model\ResourceModel\Field\CollectionFactory $fieldsCollectionFactory,
        \CSSoft\CheckoutDetails\Model\FieldFactory $fieldFactory,
        \CSSoft\CheckoutDetails\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->fieldValuesCollectionFactory = $fieldValuesCollectionFactory;
        $this->fieldsCollectionFactory = $fieldsCollectionFactory;
        $this->fieldFactory = $fieldFactory;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeId = $this->storeManager->getStore()->getId();

        $fieldValuesCollection = $this->fieldValuesCollectionFactory
            ->create()
            ->addStoreFilter($storeId)
            ->addQuoteFilter($order->getQuoteId())
            ->load();

        if (!$fieldValuesCollection->count()) {
            return $this;
        }

        $fieldsCollection = $this->fieldsCollectionFactory
            ->create()
            ->addFieldToFilter('field_id', ['in' => $fieldValuesCollection->getColumnValues('field_id')])
            ->load();

        foreach ($fieldValuesCollection as $fieldValue) {
            // convert client date to mysql format
            if ($fieldsCollection->getItemById($fieldValue->getFieldId())->getFrontendInput() === 'date') {
                $date = $this->helper->parseDateString($fieldValue->getValue());
                $fieldValue->setValue($date->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
            }
            $fieldValue->setOrderId($order->getId())->save();
        }

        return $this;
    }
}

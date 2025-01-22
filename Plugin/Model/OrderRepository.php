<?php
namespace CSSoft\CheckoutDetails\Plugin\Model;

class OrderRepository
{
    /**
     * @var \CSSoft\CheckoutDetails\Helper\Data
     */
    private $helper;

    /**
     * @param \CSSoft\CheckoutDetails\Helper\Data $helper
     */
    public function __construct(
        \CSSoft\CheckoutDetails\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $entity
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderInterface $entity
    ) {
        if ($this->helper->isEnabled()) {
            $this->setCssoftCheckoutFields($entity);
        }

        return $entity;
    }

    /**
     *
     * @param \Magento\Sales\Api\OrderRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    public function afterGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Sales\Api\Data\OrderSearchResultInterface $searchResult
    ) {
        foreach ($searchResult->getItems() as $order) {
            $this->setCssoftCheckoutFields($order);
        }

        return $searchResult;
    }

    /**
     * Set checkout fields to extension attributes
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    private function setCssoftCheckoutFields(
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $fields = $this->helper->getOrderFieldsValues($order);
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes->setCssoftCheckoutFields($fields);
        $order->setExtensionAttributes($extensionAttributes);
    }
}

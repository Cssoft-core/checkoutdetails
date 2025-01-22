<?php
namespace CSSoft\CheckoutDetails\Model\ResourceModel\Field;

class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     * @codeCoverageIgnore
     */
    protected function _construct()
    {
        $this->_init('cssoft_checkoutdetails_field_option', 'option_id');
    }
}

<?php
namespace CSSoft\CheckoutDetails\Block\Adminhtml;

/**
 * Adminhtml checkout fields content block
 */
class Field extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'CSSoft_CheckoutDetails';
        $this->_controller = 'adminhtml_field';
        $this->_headerText = __('Checkout Fields');
        $this->_addButtonLabel = __('Add New Field');
        parent::_construct();
    }
}

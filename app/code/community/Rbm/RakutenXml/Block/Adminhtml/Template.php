<?php


/**
 * Adminhtml cms pages content block
 *
 * @category   Rbm
 * @package    Rbm_Template
 * @author      Reinaldo Mendes <reinaldorock@gmail.com>
 */
class Rbm_Template_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'rbmTemplate_adminhtml';
        $this->_controller = 'template';
        $this->_headerText = Mage::helper('rbmTemplate')->__('Manage Templates');
        

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('add', 'label', Mage::helper('rbmTemplate')->__('Add New Template'));
        } else {
            $this->_removeButton('add');
        }

    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/rbmTemplate');
    }

}

<?php


/**
 * Adminhtml cms pages content block
 *
 * @category   Rbm
 * @package    Rbm_Template
 * @author      Reinaldo Mendes <reinaldorock@gmail.com>
 */

class Rbm_Template_Block_Adminhtml_Template_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('rbmTemplate_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rbmTemplate')->__('Template Information'));
    }
}

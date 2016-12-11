<?php


/**
 * Adminhtml cms pages content block
 *
 * @category   Rbm
 * @package    Rbm_Template
 * @author      Reinaldo Mendes <reinaldorock@gmail.com>
 */

class Rbm_Template_Block_Adminhtml_Template_Edit_Tab_Content
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Mage_Cms_Model_Page */
        $model =  Mage::getSingleton('rbmTemplate/template');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new Varien_Data_Form();
         $form->setHtmlIdPrefix('rbmTemplate_');

        $fieldset = $form->addFieldset('content_fieldset', array('legend'=>Mage::helper('rbmTemplate')->__('Template Information')));


        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('rbmTemplate')->__('Content'),
            'title'     => Mage::helper('rbmTemplate')->__('Content'),            
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));

        


        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('rbmTemplate')->__('Template Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('rbmTemplate')->__('Template Content');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return Mage::getSingleton('rbmTemplate/template')->getId() > 0;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
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

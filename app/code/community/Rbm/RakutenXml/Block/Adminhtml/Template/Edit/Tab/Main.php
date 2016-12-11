<?php


/**
 * Adminhtml cms pages content block
 *
 * @category   Rbm
 * @package    Rbm_Template
 * @author      Reinaldo Mendes <reinaldorock@gmail.com>
 */

class Rbm_Template_Block_Adminhtml_Template_Edit_Tab_Main
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

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('rbmTemplate')->__('Template Information')));

        if ($model->getId()) {
            $fieldset->addField('template_id', 'hidden', array(
                'name' => 'template_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('rbmTemplate')->__('Template Title'),
            'title'     => Mage::helper('rbmTemplate')->__('Template Title'),
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));
        
        $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
            'label'     => Mage::helper('rbmTemplate')->__('Description text'),
            'title'     => Mage::helper('rbmTemplate')->__('Description text'),
            'required'  => true,
            'disabled'  => $isElementDisabled
        ));

        
        
        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => Mage::helper('rbmTemplate')->__('URL Key'),
            'title'     => Mage::helper('rbmTemplate')->__('URL Key'),
            'required'  => true,
            'class'     => 'validate-identifier',
            'note'      => $model->getUrlWithAnchor(),
            'disabled'  => $isElementDisabled
        ));


        $fieldset->addField('mime_type', 'text', array(
            'name'      => 'mime_type',
            'label'     => Mage::helper('rbmTemplate')->__('Mime Type'),
            'title'     => Mage::helper('rbmTemplate')->__('Mime Type'),
            'required'  => true,
            'default'   => 'text/xml',
            'note'      => 'text/xml, text/html, text/csv, text/json .....',
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('type', 'select', array(
            'name'      => 'type',
            'label'     => Mage::helper('rbmTemplate')->__('Template Type'),
            'title'     => Mage::helper('rbmTemplate')->__('Template Type'),
            'required'  => true,
            'options'   => $model->getAvailableTypes(),
            'disabled'  => $isElementDisabled
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('rbmTemplate')->__('Store View'),
                'title'     => Mage::helper('rbmTemplate')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'disabled'  => $isElementDisabled,
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('rbmTemplate')->__('Status'),
            'title'     => Mage::helper('rbmTemplate')->__('Page Status'),
            'name'      => 'is_active',
            'required'  => true,
            'options'   => $model->getAvailableStatuses(),
            'disabled'  => $isElementDisabled,
        ));
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

//        Mage::dispatchEvent('adminhtml_rbm_template_edit_tab_main_prepare_form', array('form' => $form));

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
        return Mage::helper('rbmTemplate')->__('Template Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('rbmTemplate')->__('Template Information');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
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

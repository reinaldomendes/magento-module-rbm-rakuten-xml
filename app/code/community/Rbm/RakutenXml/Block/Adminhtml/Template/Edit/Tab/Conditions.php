<?php


/**
 * Adminhtml cms pages content block
 *
 * @category   Rbm
 * @package    Rbm_Template
 * @author      Reinaldo Mendes <reinaldorock@gmail.com>
 */

class Rbm_Template_Block_Adminhtml_Template_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('rbmTemplate')->__('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('rbmTemplate')->__('Conditions');
    }

    /**
     * Returns status flag about this tab can be showen or not
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

    protected function _prepareForm()
    {
        $model =  Mage::getSingleton('rbmTemplate/template');

        //$form = new Varien_Data_Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rbmTemplate_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/*/newConditionHtml/form/rbmTemplate_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('salesrule')->__('Apply the rule only if the following conditions are met (leave blank for all products)')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('rbmTemplate')->__('Conditions'),
            'title' => Mage::helper('rbmTemplate')->__('Conditions'),
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}

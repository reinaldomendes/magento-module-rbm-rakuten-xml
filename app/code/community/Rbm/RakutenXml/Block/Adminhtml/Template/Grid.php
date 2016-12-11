<?php


/**
 * Adminhtml cms pages content block
 *
 * @category   Rbm
 * @package    Rbm_Template
 * @author      Reinaldo Mendes <reinaldorock@gmail.com>
 */
class Rbm_Template_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
 public function __construct()
    {
        parent::__construct();
        $this->setId('rbmTemplateGrid');
        $this->setDefaultSort('title');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {        
        $collection = Mage::getModel('rbmTemplate/template')->getCollection();
        /* @var $collection Rbm_Template_Model_Resource_Template_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
//        $baseUrl = $this->getUrl();

        $this->addColumn('title', array(
            'header'    => Mage::helper('rbmTemplate')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('rbmTemplate')->__('Identifier'),
            'align'     => 'left',
            'index'     => 'code'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('rbmTemplate')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

        
        
        $this->addColumn('type', array(
            'header'    => Mage::helper('rbmTemplate')->__('Template Type'),
            'index'     => 'type',
            'type'      => 'options',
            'options' => Mage::getSingleton('rbmTemplate/template')->getAvailableTypes(),
        ));

        $this->addColumn('mime_type', array(
            'header'    => Mage::helper('rbmTemplate')->__('Content Type'),
            'index'     => 'mime_type',
            'type'      => 'text',
        ));
        
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('rbmTemplate')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('rbmTemplate/template')->getAvailableStatuses()
        ));
        
         $this->addColumn('url', array(
            'header'    => Mage::helper('rbmTemplate')->__('Url'),
            'getter'     => 'getUrlWithAnchor',
            'type'      => 'text',
            'filter' => false
        ));

        return parent::_prepareColumns();
    }

//    protected function _afterLoadCollection()
//    {
//        $this->getCollection()->walk('afterLoad');
//        parent::_afterLoadCollection();
//    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('template_id' => $row->getId()));
    }

}

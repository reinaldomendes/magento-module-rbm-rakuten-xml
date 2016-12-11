<?php
/**
 * 
 * RAN SPI - Enterprise XML
 * Rakuten Affiliate program integration 
 * @author Reinaldo Mendes <reinaldorock@gmail.com>
 * 
 */
class Rbm_RakutenXml_Model_Resource_SentLog extends Mage_Core_Model_Resource_Db_Abstract{
    public function _construct()
    {        
        $this->_init('rbmRakutenXml/sent_log','entity_id');
    }
    protected function _beforeSave(\Mage_Core_Model_Abstract $object)
    {
        if(!$object->getId()){
            $object->setData('created_at',date('Y-m-d H:i:s'));
        }
        parent::_beforeSave($object);
    }
    
    
}
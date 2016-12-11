<?php
/**
 * 
 * RAN SPI - Enterprise XML
 * Rakuten Affiliate program integration 
 * @author Reinaldo Mendes <reinaldorock@gmail.com>
 * 
 */
class Rbm_RakutenXml_Model_Resource_Commission extends Mage_Core_Model_Resource_Db_Abstract{
    public function _construct()
    {        
        $this->_init('rbmRakutenXml/commission','entity_id');
    }
    
    
}
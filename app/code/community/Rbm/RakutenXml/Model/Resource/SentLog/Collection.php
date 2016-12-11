<?php
/**
 * 
 * RAN SPI - Enterprise XML
 * Rakuten Affiliate program integration 
 * @author Reinaldo Mendes <reinaldorock@gmail.com>
 * 
 */

class Rbm_RakutenXml_Model_Resource_SentLog_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('rbmRakutenXml/sentLog');
    }
}
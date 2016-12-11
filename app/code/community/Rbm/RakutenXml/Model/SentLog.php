<?php
/**
 * 
 * RAN SPI - Enterprise XML
 * Rakuten Affiliate program integration 
 * @author Reinaldo Mendes <reinaldorock@gmail.com>
 * 
 */

class Rbm_RakutenXml_Model_SentLog extends Mage_Core_Model_Abstract
{
    const STATUS_GOOD = 1;
    const STATUS_BAD = -1;
    const STATUS_FAIL = -2;

    public function _construct()
    {
        parent::_construct();
        $this->_init('rbmRakutenXml/sentLog');
    }

}

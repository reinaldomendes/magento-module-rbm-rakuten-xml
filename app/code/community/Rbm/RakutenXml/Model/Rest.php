<?php

/**
 * 
 * RAN SPI - Enterprise XML
 * Rakuten Affiliate program integration 
 * @author Reinaldo Mendes <reinaldorock@gmail.com>
 * 
 */
class Rbm_RakutenXml_Model_Rest
{

    const REST_URL = 'https://track.linksynergy.com/xml';

    /**
     *
     * @var Rbm_RakutenXml_Helper_Data
     */
    protected $_helper;

    /**
     * 
     * @var Mage_Sales_Model_Order
     */
    protected $_order;
    
    
    /*
     * @var Rbm_RakutenXml_Model_Commission
     */
    protected $_commission;

    public function __construct(array $options)
    {
        if (!isset($options['helper'])) {
            throw new Exception('"helper" option is required');
        }
        if (!isset($options['order'])) {
            throw new Exception('"order" option is required');
        }

        $this->setHelper($options['helper']);
        $this->setOrder($options['order']);
    }

    /**
     * 
     * 
     * @param Mage_Sales_Model_Order $order
     * @return \Rbm_RakutenXml_Model_Rest
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * 
     * @param Rbm_RakutenXml_Helper_Data $helper - Merchant ID
     * @return \Rbm_RakutenXml_Model_Rest
     */
    public function setHelper($helper)
    {
        $this->_helper = $helper;
        return $this;
    }
    
    protected function _getCommission(){
        if(null == $this->_commission){
            $commission = Mage::getModel('rbmRakutenXml/commission')->load($this->_order->getId(),
                    'order_id');
            $this->_commission = $commission;
        }
        return $this->_commission;
    }

    /**
     * notify commissioned actions
     */
    public function notifyRakuten($isCancel = false)
    {
        
        
        $commission = $this->_getCommission();
        
        
        if(!$commission->getId()){//no commission
            return null;
        }
        
        $sentToRakuten = (int)$commission->getData('sent_to_rakuten');
        if($sentToRakuten === -1 && $isCancel){ //already canceled 
            return null;
        }
        if($sentToRakuten === 0 && $isCancel){ //no sent, no cancelation            
            return null;
        }        
        if($sentToRakuten === 1 && !$isCancel){//already sent
            return null;
        }
        
        
        
        
        
        //‘mid=’ [um número estático e constante (merchant ID) Providenciado pelo time da Rakuten]                
        $mid = $this->_helper->getRanMID();
        

        //‘msg=’ [informação de transação em base64 encode]
        $msg = $this->_buildMessage($isCancel);        
        $msg = $this->_encode($msg);

        //‘md5=’ [Message Authentication Code (MAC) Computed usando HMAC-MD5. Também em base64 encode]        
        $md5 = $this->_md5Hmac($msg);
        $md5 = $this->_encode($md5);

        $xml = 1;        

       
        $result = $this->_sendPost(array(
            'mid' => $mid,
            'md5' => $md5,
            'msg' => $msg,
            'xml' => $xml
        ));
        $content = $result['content'];            
        $log = Mage::getModel('rbmRakutenXml/sentLog');
        $log->setContent(var_export(array(
            'msg' => $msg,
            'md5' => $md5,
            'mid' => $mid,
            'xml' => $xml,
            'response' => $result
        ),true));
        $log->setHttpCode($result['http_code']);
        $log->setCommissionId($commission->getId());

        $xmlContent = simplexml_load_string($content);
        if($log->getHttpCode() !== 200){
            $log->setStatus(Rbm_RakutenXml_Model_SentLog::STATUS_HTTP_FAIL);
        }
        elseif($xmlContent->response->error){
            $log->setStatus(Rbm_RakutenXml_Model_SentLog::STATUS_FAIL);
        }else{
            $transactions = $xmlContent->response->summary->transactions;
            if((int)$transactions->bad > 0){
                $log->setStatus(Rbm_RakutenXml_Model_SentLog::STATUS_BAD);
            }else{//sent success
                
                $log->setStatus(Rbm_RakutenXml_Model_SentLog::STATUS_GOOD);
                $sentToRakuten = $isCancel ? -1 : 1;
                $commission->setData('sent_to_rakuten',$sentToRakuten);
                $commission->save();
            }
        }
        $log->save();
        #die('entrou');
        
    }

    protected function _sendPost($fields)
    {
        $ch = curl_init(self::REST_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return array('http_code' => $info['http_code'], 'content' => $result);
    }

    /**
     * 
     * <message xmlns="http://www.linkshare.com/namespaces/realtime-transactions-1.0">
      <sku_order>
      <orderid>12345</orderid>
      <siteid>lKW2Xiq9xN0-4.c_9w1X8ZpO94TUl4hg3D</siteid>
      <time_entered>2011-06-18T02:20:00Z</time_entered>
      <currency>USD</currency>
      <trans_date>2011-06-18T20:02:00Z</trans_date>
      <item>
      <sku>productA_sku</sku>
      <quantity>3</quantity>
      <amount>30000</amount>
      <product_name>product A</product_name>
      </item>
      <item>
      <sku>productB_sku</sku>
      <quantity>1</quantity>
      <amount>1000</amount>
      <product_name>product B</product_name>
      </item>
      </sku_order>
      </message>
     * 
     * @param boolean $positive - if price send as positive or negative
     */
    protected function _buildMessage($isCancel = false)
    {
        $order = $this->_order;
        




        $commission = $this->_getCommission();
        if (!$commission->getId()) {
            return null;
        }
        
        
        
        $object = new Varien_Object();
        $object->setData('orderid', $order->getId());
        $object->setData('siteid', $commission->getData('site_id'));
        $object->setData('time_entered',
                $this->_formatDateRakuten($commission->getData('datetime_entered')));
        $object->setData('currency', $order->getStoreCurrencyCode());
        $object->setData('trans_date',
                $this->_formatDateRakuten($order->getData('created_at')));

        $result = $object->toXml(array('orderid', 'siteid', 'time_entered', 'currency',
            'trans_date'), null);
        foreach ($order->getAllVisibleItems() as $item) {
            /* @var $item Mage_Sales_Model_Order_Item */
            $_item = new Varien_Object;


            $_item->setData('sku', $item->getSku());
            $_item->setData('quantity', round($item->getQtyInvoiced()));

            $ammount = round($item->getPriceInclTax() - $item->getDiscountAmount(),
                    2);
            $ammount *= $isCancel ? -1 : 1;
            $_item->setData('ammount', $ammount);

            $_item->setData('product_name', $item->getName());

            $itemXml = $_item->toXml(array('sku', 'quantity', 'ammount', 'product_name'),
                    null);

            $result .= <<<EOD
    <item>
    {$itemXml}
    </item>
EOD;
        }
        $result = <<<EOD
<message xmlns="http://www.linkshare.com/namespaces/realtime-transactions-1.0">
    <sku_order>{$result}</sku_order>            
</message>
EOD;



        return $result;
    }

    protected function _md5Hmac($message)
    {
        return mhash(MHASH_MD5, $message, $this->_helper->getHmacKey());
    }

    /**
     * 
     * @param string $value - datetime 'Y-m-d H:i:s'
     * @return string - datetime formated 'Y-m-d\TH:i:s\Z'
     */
    protected function _formatDateRakuten($value)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $value);
        /* @var $date DateTime */
        return Mage::getModel('core/date')->date('Y-m-d\TH:i:s\Z',
                        $date->getTimestamp());
    }

    /**
     * 
     * @param string $message
     * @return string base64 string replaced (+ /) => (- _) respectivelly
     */
    protected function _encode($message)
    {
        $_message = base64_encode($message);
        return strtr($_message, array('+' => '-', '/' => '_'));
    }

    /**
     * decode base 64 with replaced (+ /) => (- _) respectivelly
     * @param string $message
     * @return string base64 string decoded
     */
    protected function _decode($message)
    {
        $_message = strtr($message, array('-' => '+', '_' => '/'));
        return base64_decode($_message);
    }

}

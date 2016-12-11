<?php
/**
 * 
 * RAN SPI - Enterprise XML
 * Rakuten Affiliate program integration 
 * @author Reinaldo Mendes <reinaldorock@gmail.com>
 * 
 */

class Rbm_RakutenXml_Helper_Data extends Mage_Core_Helper_Abstract{

  const COOKIE_NAME = 'rakuten_gateway_access';
  
  
  
  
  public function initCookie($siteId){
    if(!$this->isEnabled()){
        return false;
    }
    //iniciar cookie não sei o nome.
    $cookieName = self::COOKIE_NAME;
    //o cookie deve conter Data Atual e Hora
    $value = array('site_id' => $siteId, 'time_entered' => date('Y-m-d H:i:s'));

    //o cookie expira em 2 anos.
    $twoYears = 60 * 60 * 24 * 365 * 2;
    $expires =  time() + $twoYears;
    setcookie(self::COOKIE_NAME,json_encode($value), $expires,'/');
    return true;
  }
  
  public function getCookieValues(){      
      if(isset($_COOKIE[self::COOKIE_NAME])){
        return json_decode($_COOKIE[self::COOKIE_NAME],true);
      }
      return null;
  }
  
  
  public function isEnabled(){
      return Mage::getStoreConfig('rbmRakuten/enterprise_xml/enabled');
  }
  
  public function getRanMID(){      
      return Mage::getStoreConfig('rbmRakuten/enterprise_xml/ran_mid');
  }
  
  public function getGlobalScript(){      
      return Mage::getStoreConfig('rbmRakuten/enterprise_xml/global_code');
  }
  /**
   * 
   * @return type
   */
  public function getConversionScript(Mage_Sales_Model_Order $order){      
      if($this->isConversionPage()){
        
        $customerOrdersCount = Mage::getResourceModel('sales/order_collection')
                ->addFieldToFilter('customer_id',$order->getCustomerId())
                ->count();
        $customerStatus = $customerOrdersCount > 1 ? 'Existing' : 'New';
        
        
        $rmTrans = array(
                'orderid' => $order->getId(),
                'currency' => $order->getStoreCurrencyCode(),
                'customerStatus' => $customerStatus,
                'conversionType' => 'Sale',
                'allowCommission' => $this->getAllowCommission(),  
                'customerID' => $order->getCustomerId(),
                'discountCode' =>  trim($order->getData('coupon_code')),
                'discountAmount' =>  $order->getDiscountAmount(),
                'taxAmount' => $order->getTaxAmount(),
                'ranMID' => $this->getRanMID(),
                'lineitems' => array()
        );
        foreach($order->getAllVisibleItems() as $item){
            /*@var $item Mage_Sales_Model_Order_Item*/
            $rmTrans['lineitems'][] = array(
                'quantity' => $item->getQtyOrdered(),
                'unitPrice'  => $item->getPriceInclTax(),
                'unitPriceLessTax' => $item->getPrice(),
                'SKU' => $item->getSku(),
                'productName' => $item->getName()
            );
        }
        
        //replace rm_trans variable
        $regex = '/var\s+rm_trans[^;\/]+;?/m';        
        $rmTransCode = 'var rm_trans = ' . json_encode($rmTrans) . ';';        
        
        $result =  Mage::getStoreConfig('rbmRakuten/enterprise_xml/conversion_code');
        $result = preg_replace('@</?script[^>]*>@', '', $result);
        $result = preg_replace('@<!--.*?>@', '', $result);        
        $result = preg_replace($regex, '', $result, 1);
        
        
        $result = <<<EOD
            <!-- START of Rakuten Marketing Conversion Tag -->
            <script type='text/javascript'>
            $rmTransCode
            $result
            </script>
            <!-- END of Rakuten Marketing Conversion Tag -->
EOD;
        
        return $result;
      }
      return null;
  }
  
  
  
  
  /**
   * 
   * @return string
   */
  public function getAllowCommission(){
      return 'true';
  }
  
  
  /**
   * 
   * @return type
   */
  public function isConversionPage(){
    $uri = Mage::app()->getRequest()->getRequestUri();

    $uri = str_replace('//','/',$uri);
    $values = preg_split('@[\r\n]+@', Mage::getStoreConfig('rbmRakuten/enterprise_xml/success_pages_paths'));
    array_walk($values, function(&$v){
        $v = str_replace('//','/',$v);
        $v = trim($v);
    });
    $values = array_filter($values);
    return in_array($uri,$values);
  }

  
}

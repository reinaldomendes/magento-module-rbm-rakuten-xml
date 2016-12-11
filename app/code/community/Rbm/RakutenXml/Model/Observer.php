<?php
/**
 * 
 * RAN SPI - Enterprise XML
 * Rakuten Affiliate program integration 
 * @author Reinaldo Mendes <reinaldorock@gmail.com>
 * 
 * This class save observe an order made with a rakuten cookie and sent save 
 * the necessary info om commission model
 * 
 */
class Rbm_RakutenXml_Model_Observer{
    public function onOrderPlace($evt){
        $helper = Mage::helper('rbmRakutenXml');        
        if(!$helper->isEnabled()){
            return ;
        }
        
        
        $order = $evt->getOrder();        
        $values = $helper->getCookieValues();
        if(null !== $values){
            $model = Mage::getModel('rbmRakutenXml/commission');
            $model->setOrderId($order->getId())
                    ->setSiteId($values['site_id'])
                    ->setDatetimeEntered($values['time_entered'])
                    ->save();            
        }
        
        
    }
    
    public function addJsCodes($evt){
         $helper = Mage::helper('rbmRakutenXml');   
         if(!$helper->isEnabled()){
            return ;
         }
         
         $layout = Mage::getSingleton('core/layout');
         /*@var $layout Mage_Core_Model_Layout*/
         $head = $layout->getBlock('head');
         /*@var $head Mage_Page_Block_Html_Head*/
         if($head){
            $coreCodeBlock = $layout->createBlock('core/text');
            $coreCodeBlock->setText($helper->getGlobalScript());         

            if($helper->isConversionPage() && $helper->getCookieValues()){             
               $text = $coreCodeBlock->getText();
               $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
               $order = Mage::getModel('sales/order')->load($orderId);
               $coreCodeBlock->setText($text . "\n" . $helper->getConversionScript($order));

            }

            $head->append($coreCodeBlock);         
         }
    }
    
    
}
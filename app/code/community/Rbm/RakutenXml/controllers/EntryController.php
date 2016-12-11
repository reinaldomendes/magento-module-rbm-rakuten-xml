<?php

class Rbm_RakutenXml_EntryController extends Mage_Core_Controller_Front_Action{

    public function indexAction(){

      $request = $this->getRequest();
      $helper = Mage::helper('rbmRakutenXml');

      $siteId = $request->getParam('siteID');
      $helper->initCookie($siteId);
      $url = $request->getParam('url');

      return $this->_redirectUrl($url);
    }
    // public function viewCookieAction(){        
    //     $helper = Mage::helper('rbmRakutenXml');
    //     print_r($helper->getCookieValues());die;
    // }



}

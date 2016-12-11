<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Stynaxhighlighter
 *
 * @author reinaldo
 */
class Rbm_Template_Block_Rewrite_Stynaxhighlighter extends Plugincompany_Syntaxhighlighter_Block_Syntaxhighlighter
{
    public function getTextAreas(){
        $controller = Mage::app()->getRequest()->getControllerName();
        switch($controller){
            case 'rbmTemplate_template':
                $typeArray = explode('/',Mage::getSingleton('rbmTemplate/template')->getMimeType());
                $type = end($typeArray);
                $editorFields = array(
                        array(
                            'title' => 'rbmTemplate_content',
                            'type' => $type
                        )                       
                    );
                return Zend_Json::encode($editorFields,false,array('enableJsonExprFinder'=>true));
            default:
                return parent::getTextAreas();            
        }
    }
}

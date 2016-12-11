<?php
$installer = $this;
/*@var $installer Rbm_RakutenXml_Model_Resource_Setup*/
$installer->startSetup();
$conn = $installer->getConnection();

/*@var $conn Varien_Db_Adapter_Interface*/
$commissionedActionsTableName = $installer->getTable('rbmRakutenXml/commission');
$commissionedActionsTable = $conn->newTable($commissionedActionsTableName)
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
        ->addColumn('site_id', Varien_Db_Ddl_Table::TYPE_TEXT,255,array(
            'nullable'  => false,
        ))
        ->addColumn('datetime_entered', Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(
            'nullable'  => false,
        ))

        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
            'nullable'  => false,
            'unsigned'  => true,
        ))
        ->addColumn('sent_to_rakuten', Varien_Db_Ddl_Table::TYPE_SMALLINT,null,array(
            'nullable'  => false,
            'default' => 0
        ))        
        ->addForeignKey($installer->getFkName('rbmRakutenXml/commission', 'order_id', 'sales/order', 'entity_id'),
        'order_id', $installer->getTable('sales/order'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addIndex($installer->getIdxName('rbmRakutenXml/commission', array('order_id')), array('order_id'),array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));
        

$conn->dropTable($commissionedActionsTableName);
$conn->createTable($commissionedActionsTable);




$commissionedActionsSentLogTableName = $installer->getTable('rbmRakutenXml/sent_log');
$commissionedActionsSentLogTable = $conn->newTable($commissionedActionsSentLogTableName)
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
        ->addColumn('content', Varien_Db_Ddl_Table::TYPE_TEXT,null,array(
            'nullable'  => false,
        ))
        
        ->addColumn('http_code', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
            'nullable'  => false,
        ))

        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
            'nullable'  => false,
        ))
        ->addColumn('commission_id', Varien_Db_Ddl_Table::TYPE_INTEGER,null,array(
            'unsigned'  => true,
            'nullable'  => false,
        ))  
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME,null,array(
            'nullable'  => false,
        ))
        
        ->addForeignKey($installer->getFkName('rbmRakutenXml/sent_log', 'commission_id', 'rbmRakutenXml/commission', 'entity_id'),
        'commission_id', $installer->getTable('rbmRakutenXml/commission'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
        

$conn->dropTable($commissionedActionsSentLogTableName);
$conn->createTable($commissionedActionsSentLogTable);





$installer->endSetup();

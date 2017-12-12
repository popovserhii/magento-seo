<?php
/**
 * @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup
 */
$installer = $this;
$installer->startSetup();
$tables = array(
    $installer->getTable('catalog/category_anchor_products_indexer_idx'),
    $installer->getTable('catalog/category_anchor_products_indexer_tmp')
);
foreach ($tables as $table) {
    $installer->getConnection()->modifyColumn(
        $table,
        'position',
        array(
            'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => FALSE,
            'nullable' => TRUE,
            'default'  => null,
            'comment'  => 'Position'
        )
    );
}
$installer->endSetup();
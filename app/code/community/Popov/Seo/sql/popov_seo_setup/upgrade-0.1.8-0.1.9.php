<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableRule = $installer->getTable('popov_seo/rule');

$installer->startSetup();

$conn = $installer->getConnection();
$conn->addColumn($tableRule, 'conditions_serialized',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        //'length'    => 16,
        'nullable'  => false,
        #'after'     => 'seo_attributes',
        'comment'   => 'Serialized condition rules'
    )
);
$conn->dropColumn($tableRule, 'seo_option_filters');

$installer->endSetup();
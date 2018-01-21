<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableRule = $installer->getTable('popov_seo/rule');

$installer->startSetup();

$conn = $installer->getConnection();
$conn->addColumn(
    $tableRule,
    'context',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 16,
        'nullable'  => false,
        'after'     => 'seo_option_filters',
        'comment'   => 'Context type: meta_title, meta_description, meta_keywords, h1, description, etc.'
    )
);
$conn->dropColumn($tableRule, 'title');
$conn->dropColumn($tableRule, 'description');
$conn->dropColumn($tableRule, 'keywords');
//$conn->dropColumn($tableRule, 'h1');

$installer->endSetup();
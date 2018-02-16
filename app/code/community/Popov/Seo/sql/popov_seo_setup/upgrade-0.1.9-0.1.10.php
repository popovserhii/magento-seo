<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableRule = $installer->getTable('popov_seo/rule');

$installer->startSetup();

$conn = $installer->getConnection();
$conn->addColumn($tableRule, 'name',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255, // @see https://magento.stackexchange.com/a/9644/59292
        'nullable'  => false,
        'after'     => 'type',
        'comment'   => 'Rule name'
    )
);
$conn->addColumn($tableRule, 'priority',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'default'   => 0,
        'after'    => 'updated_at',
        'comment'   => 'Higher priority means the rule is checked first. By default, the first attached route is read.'
    )
);

$installer->endSetup();
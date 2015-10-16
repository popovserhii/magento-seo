<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$tableRule = $installer->getTable('agere_seo/rule');

$installer->run("
	/*Table structure for table `agere_seo_rule` */
    ALTER TABLE {$tableRule}
    CHANGE COLUMN `date_created` `created_at` DATETIME NOT NULL default '0000-00-00 00:00:00' AFTER `seo_attribute_filters`, /* cannot create DSL structure for default datetime */
    ADD COLUMN `updated_at` DATETIME NOT NULL default '0000-00-00 00:00:00'; /* @see http://magento.stackexchange.com/a/3217 */
");

$installer->getConnection() // @link http://magento.stackexchange.com/a/4617
    ->addColumn($tableRule, 'is_active', array( // instead can use string "tinyint(1) UNSIGNED DEFAULT 0 AFTER store_id"
        'type'     => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'length'   => '1',
        'nullable' => false,
        'default'  => 1,
        'comment'  => 'Is rule active?'
    ));

$installer->endSetup();
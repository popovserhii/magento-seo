<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
	/*Table structure for table `agere_seo_rule` */

    ALTER TABLE {$installer->getTable('agere_seo/rule')}
    ADD COLUMN `seo_attribute_filters` varchar(255) DEFAULT NULL AFTER `seo_attributes`;
");

$installer->endSetup();
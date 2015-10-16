<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
	/*Table structure for table `agere_seo_rule` */

    ALTER TABLE {$installer->getTable('agere_seo/rule')}
    ADD COLUMN `h1_title` varchar(255) DEFAULT NULL AFTER `keywords`;
");

$installer->endSetup();
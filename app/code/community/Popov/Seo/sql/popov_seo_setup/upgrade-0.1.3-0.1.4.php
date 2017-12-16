<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
	/*Table structure for table `popov_seo_rule` */

    ALTER TABLE {$installer->getTable('popov_seo/rule')}
    ADD COLUMN `h1` varchar(255) DEFAULT NULL AFTER `keywords`,
    ADD COLUMN `content` TEXT DEFAULT NULL AFTER `h1`;
");

$installer->endSetup();
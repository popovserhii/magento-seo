<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("

/*Table structure for table `agere_seo_block` */

CREATE TABLE IF NOT EXISTS {$this->getTable('agere_seo_block')} (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` smallint(5) unsigned NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `seo_text` text NOT NULL,
  `is_active` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");

$installer->endSetup();
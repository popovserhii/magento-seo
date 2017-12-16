<?php
/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_<package>
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 16.10.14 12:29
 */

class Popov_Seo_Model_Resource_Seoblock extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
	{
		$this->_init('popov_seo/seoblock', 'entity_id');
	}

}
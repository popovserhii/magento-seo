<?php
/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_<package>
 * @author Popov Sergiy <popov@popov.com.ua>
 * @datetime: 13.09.13 14:31
 */

class Popov_Seo_Model_Resource_Seoblock_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('popov_seo/seoblock');
	}

}
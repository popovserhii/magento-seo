<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 16.10.14 12:29
 */

class Agere_Seo_Model_Resource_Seoblock extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
	{
		$this->_init('agere_seo/seoblock', 'entity_id');
	}

}
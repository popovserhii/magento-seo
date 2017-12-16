<?php
/**
 * Rule resource
 *
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 21.04.14 15:22
 */

class Popov_Seo_Model_Resource_Rule extends Mage_Core_Model_Resource_Db_Abstract {

	protected function _construct() {
		$this->_init('popov_seo/rule', 'id');
	}

}
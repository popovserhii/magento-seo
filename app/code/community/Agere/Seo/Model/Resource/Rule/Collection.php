<?php
/**
 * Rule collection
 *
 * @category Agere
 * @package Agere_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 21.04.14 15:22
 */

class Agere_Seo_Model_Resource_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

	protected function _construct() {
		$this->_init('agere_seo/rule');
	}

	public function addStoreFilter($store, $withAdmin = true){
		if ($store instanceof Mage_Core_Model_Store) {
			$store = array($store->getId());
		}

		if (!is_array($store)) {
			$store = array($store);
		}

		$this->addFilter('store_id', array('in' => $store));

		return $this;
	}

}
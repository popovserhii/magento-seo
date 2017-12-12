<?php
/**
 * @category    Popov
 * @package     Popov_Seo
 * @copyright   Copyright (c) http://popov.com.ua
 * @license     http://www.manadev.com/license  Proprietary License
 * @author      Popov Sergiy
 */
class Popov_Seo_Model_FilterLinks_Noindex_Any {

	public function detect($layerModel) {
		$filter = null;
		$result = false;
		foreach (Mage::getSingleton($layerModel)->getState()->getFilters() as $item) {
			$result = true;
			break;
		}

		return $result;
	}

}
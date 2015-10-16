<?php
/**
 * @category    Agere
 * @package     Agere_Seo
 * @copyright   Copyright (c) http://agere.com.ua
 * @license     http://www.manadev.com/license  Proprietary License
 * @author      Popov Sergiy
 */
class Agere_Seo_Model_FilterLinks_Noindex_Any {

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
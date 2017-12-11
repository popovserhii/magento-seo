<?php
/**
 * @category    Agere
 * @package     Popov_Seo
 * @copyright   Copyright (c) http://agere.com.ua
 * @license     http://www.manadev.com/license  Proprietary License
 * @author      Popov Sergiy
 */
class Popov_Seo_Model_FilterLinks_Noindex_Options {

	public function detect($layerModel) {
		$filters = array();
		$result = false;
		foreach (Mage::getSingleton($layerModel)->getState()->getFilters() as $item) {
			$code = $item->getFilter()->getRequestVar();
			if (!isset($filters[$code])) {
				$filters[$code] = $code;
			} else {
				$result = true;
				break;
			}
		}

		return $result;
	}

}
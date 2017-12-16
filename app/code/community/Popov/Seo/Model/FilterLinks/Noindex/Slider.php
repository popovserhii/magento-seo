<?php
/**
 * @category    Popov
 * @package     Popov_Seo
 * @copyright   Copyright (c) http://popov.com.ua
 * @license     http://www.manadev.com/license  Proprietary License
 * @author      Serhii Popov
 */
class Popov_Seo_Model_FilterLinks_Noindex_Slider {

	public function detect($layerModel) {
		$filter = null;
		$result = false;
		foreach (Mage::getSingleton($layerModel)->getState()->getFilters() as $item) {
			if ($item->getFilter()->getFilterOptions() && $item->getFilter()->getFilterOptions()->getDisplay() == 'slider') {
				$result = true;
				break;
			}
		}

		return $result;
	}

}
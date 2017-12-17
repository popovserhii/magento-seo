<?php
/**
 * @category    Popov
 * @package     Popov_Seo
 * @copyright   Copyright (c) http://popov.com.ua
 * @license     http://www.manadev.com/license  Proprietary License
 * @author      Serhii Popov
 */
class Popov_Seo_Model_FilterLinks_Index_Filters {

	public function detect($layerModel, $rule = '') {
		/**
		 * @var Mage_Catalog_Model_Layer_State $result
		 * @var Mana_Filters_Model_Item $item
		 */
		$result = false;
		$activeAttr = array();

		if ($rule) {
			foreach (Mage::getSingleton($layerModel)->getState()->getFilters() as $item) {
				$attr = $item->getFilter()->getAttributeModel()->getFrontend()->getAttribute();
				$activeAttr[$attr->getAttributeCode()][] = $attr;
			}

			$ruleProcessed = $this->processRule($rule);			
			foreach ($activeAttr as $attrCode => $attr) {
				if (isset($ruleProcessed[$attrCode]) && $ruleProcessed[$attrCode] === count($activeAttr[$attrCode])) {
					$result = true;
				} else {
					$result = false;
					break;
				}
			}
		}

		return $result;
	}

	protected function processRule($rule) {
		preg_match_all('/([0-9\s]+)([a-z_]+)/', $rule, $matches);

		$matchedString = array_shift($matches);
		$numbers = array_shift($matches);
		$attributes = array_shift($matches);

		$rules = array();
		foreach ($attributes as $key => $attribute) {
			$rules[trim($attribute)] = (int) trim($numbers[$key]);
		}

		return $rules;
	}

}
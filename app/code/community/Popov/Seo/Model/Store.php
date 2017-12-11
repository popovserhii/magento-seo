<?php
/**
 * @category    Agere
 * @package     Popov_Seo
 * @copyright   Copyright (c) http://www.agere.com.ua
 * @license    	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Rewrite of Mage_Core_Model_Store which makes store links SEO friendly
 *
 * @author Agere Team
 */
class Popov_Seo_Model_Store extends Mage_Core_Model_Store {

	public function getCurrentUrl($fromStore = true) {
		/** @var $core Mana_Core_Helper_Data */
		/** @var ManaPro_FilterSeoLinks_Model_Url $coreUrl */
		$core = Mage::helper('mana_core');
		$coreUrl = Mage::getSingleton('core/url');
		$conditionalWord = $core->getStoreConfig('mana_filters/seo/conditional_word');

		$request = Mage::app()->getRequest();
		$currentPath = $request->getRouteName() . '/' . $request->getControllerName() . '/' . $request->getActionName();
		$currentUrl = parent::getCurrentUrl($fromStore);

		// delete SEO friendly filters from url path in catalog
		if (($currentPath == 'catalog/category/view') && ($position = strpos($currentUrl , $conditionalWord)) !== false) {
			$parts = parse_url($currentUrl);
			$currentUrl = substr($currentUrl, 0, $position - 1) . '?' . $parts['query'];
		}

		return $coreUrl->setEscape(true)->encodeUrl('*/*/*', $currentUrl);
	}

}
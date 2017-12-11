<?php
/**
 * Overwrite "add-to-links"
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 28.06.15 22:36
 */
class Popov_Seo_Helper_Product_Compare extends Mage_Catalog_Helper_Product_Compare {

	public function getAddUrl($product) {
		/*
		 * Configurable from Admin
		 * Go to System > Configuration > Catalog: Catalog > Recently Viewed/Compared Products
		 * Set “Default Recently Compared Products” count to 0
		 * For display compare link you can put a number greater than 0
		 */
		if (Mage::getStoreConfig('catalog/recently_products/compared_count')) {
			return parent::getAddUrl($product);
		}

		return false;
	}

}
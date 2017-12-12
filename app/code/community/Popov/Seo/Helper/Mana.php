<?php
/**
 * @category Popov
 * @package Popov_Seo
 * @author Popov Sergiy <popov@popov.com.ua>
 * @datetime: 28.06.15 0:57
 */

class Popov_Seo_Helper_Mana extends Mana_Core_Helper_Data {

	public function startsWith($haystack, $needle) {
		return parent::startsWith(rtrim($haystack, '/'), rtrim($needle, '/'));
	}

}
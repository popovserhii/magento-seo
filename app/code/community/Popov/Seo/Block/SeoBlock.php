<?php
/**
 * Context Seo block
 *
 * @category Agere
 * @package Popov_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 16.10.14 15:29
 */
class Popov_Seo_Block_SeoBlock extends Mage_Core_Block_Template {

	/**
	 * @param null|array $params
	 * @return Popov_Seo_Model_Resource_Seoblock_Collection|string
	 */
	public function getSeoText($params = null) {
		/** @var Popov_Seo_Model_Seoblock $seoBlockModel */
		$seoBlockModel = Mage::getModel('popov_seo/seoblock');
		if (!empty($params) && $params['categoryId']) {
			return $seoBlockModel->getSeoText($params['storeId'], $params['categoryId'], $params['path']);
		}
		$currentCategory = Mage::registry('current_category');
		if (!$currentCategory) {
			return '';
		}
		/** @var Mage_Core_Model_Url $url */
		$url = Mage::getSingleton('core/url')->parseUrl(urldecode(Mage::helper('core/url')->getCurrentUrl()));
		$currentRouteUrl = $url->getPath();
		$path = str_replace('/' . Mage::app()->getStore()->getCode() . '/', '', $currentRouteUrl);
		$storeId = Mage::app()->getStore()->getId();

		return $seoBlockModel->getSeoText($storeId, $currentCategory->getEntityId(), $path);
	}

}
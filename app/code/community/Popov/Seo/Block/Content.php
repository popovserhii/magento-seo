<?php
/**
 * SEO content block
 *
 * @category Agere
 * @package Popov_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 13.05.14 16:22
 */
class Popov_Seo_Block_Content extends Mage_Core_Block_Template {

	/**
	 * @var Popov_Seo_Helper_Data $seoHelper
	 */
	protected $seoHelper;

	protected function _construct() {
		$this->seoHelper = Mage::helper('popov_seo');
		parent::_construct();
	}

	public function getDynamicTitle() {
		return preg_replace('/%[a-zA-Z]+%/', '', Mage::registry('seo_dynamic_title'));
	}

	public function getStaticContent() {
		$seoHelper = Mage::helper('popov_seo');
		$content = (string) Mage::getConfig()
			->getNode('global/dictionaries/popov_seo/static_content/' . $seoHelper->getSeoName());

		return $content;
	}

	public function isVisible() {
		return ((!$this->isHomePage() && $this->seoHelper->hasFilters()) || (Mage::helper('popov_seo')->getSeoName() == 'product'));
	}

	protected function isHomePage() {
		return ($this->getUrl('') == $this->getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true)));
	}

}
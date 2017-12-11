<?php
/**
 * Change meta tags before layout render
 *
 * @category Agere
 * @package Popov_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 20.04.14 15:02
 */

class Popov_Seo_Model_Observer extends Varien_Event_Observer {

	/**
	 * @var Popov_Seo_Helper_Data $seoHelper
	 */
	protected $seoHelper;

	public function controllerActionPredispatch() {
		if (!Mage::app()->getStore()->isAdmin()) {
			$this->getSeoHelper()->redirect301();
			$this->getSeoHelper()->redirectTrailingSlash();
			$this->getSeoHelper()->redirectToLowerCase();
			$this->getSeoHelper()->redirectMultipleSlashes();
			$this->getSeoHelper()->redirectWithoutStore();
			$this->getSeoHelper()->redirectNonSecureUrl();
			$this->getSeoHelper()->redirectFirstPage();
		}
	}

	public function hookToChangeMetaTags(Varien_Event_Observer $observer) {
		Popov_Seo_Model_MetaTag_Factory::create($this->getSeoHelper()->getSeoName())->run();
	}

	public function hookAfterGenerateBlocks() {
		if (!Mage::app()->getStore()->isAdmin()) {
			$this->getSeoHelper()->prepareCanonicalLink();
			$this->getSeoHelper()->prepareHreflang();
		}
	}

	public function hookTo8080Port() {
		$this->getSeoHelper()->addGoogleVerificationFor8080();
	}

	public function getSeoHelper() {
		if (!$this->seoHelper) {
			$this->seoHelper = Mage::helper('popov_seo');
		}

		return $this->seoHelper;
	}
}
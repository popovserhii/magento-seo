<?php
/**
 * Change meta tags before layout render
 *
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
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
			$this->getSeoHelper()->redirectIndexPhp();
			$this->getSeoHelper()->redirectTrailingSlash();
			$this->getSeoHelper()->redirectToLowerCase();
			$this->getSeoHelper()->redirectMultipleSlashes();
			$this->getSeoHelper()->redirectWithoutStore();
			$this->getSeoHelper()->redirectNonSecureUrl();
			$this->getSeoHelper()->redirectFirstPage();
		}
	}

	public function hookToChangeMetaTags(Varien_Event_Observer $observer) {
        //$seoName = $this->getSeoHelper()->getSeoName();
	    //if (Popov_Seo_Model_MetaTag_Factory::canCreate($seoName)) {
        //    Popov_Seo_Model_MetaTag_Factory::create($seoName)->run();
        //}
	}

	public function hookAfterGenerateBlocks() {
		if (!Mage::app()->getStore()->isAdmin()) {
			$this->getSeoHelper()->prepareMetaTags();
			$this->getSeoHelper()->prepareCanonical();
			$this->getSeoHelper()->prepareHreflang();
			$this->getSeoHelper()->prepareStaticNoindexNofollow();
		}
	}

	public function hookTo8080Port() {
		$this->getSeoHelper()->addSiteVerification();
	}

    /**
     * @return Popov_Seo_Helper_Data
     */
	public function getSeoHelper() {
		if (!$this->seoHelper) {
			$this->seoHelper = Mage::helper('popov_seo');
		}

		return $this->seoHelper;
	}
}
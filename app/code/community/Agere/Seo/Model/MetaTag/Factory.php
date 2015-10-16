<?php
/**
 * SEO meta tag factory
 *
 * @category Agere
 * @package Agere_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 20.04.14 19:21
 */

class Agere_Seo_Model_MetaTag_Factory {

	const SEO_NAMESPACE = 'Agere_Seo_Model_MetaTag';

	/**
	 * @param $name
	 * @param string $namespace
	 * @return Agere_Seo_Model_MetaTag_MetaInterface
	 */
	public static function create($name, $namespace = Agere_Seo_Model_MetaTag_Factory::SEO_NAMESPACE) {

		$className = $namespace . '_' . ucfirst($name);

		if (!class_exists($className)) {
			Mage::throwException(sprintf('Cannot found class %s', $className));
		}

		$rules = Mage::getModel('agere_seo/rule')->getCollection()
			->addFieldToFilter('type', $name)
			->addFieldToFilter('is_active', 1)
			->addFieldToFilter('store_id', array(
				array('finset' => 0), // all stores
				array('finset' => Mage::app()->getStore(true)->getId())
			))
			//->setOrder('created', 'DESC')
		;

		return new $className($rules);
	}

}
<?php
/**
 * SEO meta tag factory
 *
 * @category Agere
 * @package Popov_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 20.04.14 19:21
 */

class Popov_Seo_Model_MetaTag_Factory {

	const SEO_NAMESPACE = 'Popov_Seo_Model_MetaTag';

	/**
	 * @param $name
	 * @param string $namespace
	 * @return Popov_Seo_Model_MetaTag_MetaInterface
	 */
	public static function create($name, $namespace = Popov_Seo_Model_MetaTag_Factory::SEO_NAMESPACE) {

		$className = $namespace . '_' . ucfirst($name);

		if (!class_exists($className)) {
			Mage::throwException(sprintf('Cannot found class %s', $className));
		}

		$rules = Mage::getModel('popov_seo/rule')->getCollection()
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
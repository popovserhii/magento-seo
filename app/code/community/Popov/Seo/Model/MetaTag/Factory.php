<?php
/**
 * SEO meta tag factory
 *
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 20.04.14 19:21
 */

class Popov_Seo_Model_MetaTag_Factory {

	const SEO_NAMESPACE = 'Popov_Seo_Model_MetaTag';

	/**
	 * @param $name
	 * @param string $namespace
	 * @return Popov_Seo_Model_MetaTag_MetaInterface
	 */
	public static function create($name)
    {
		//$className = $namespace . '_' . ucfirst($name);
        //$className = self::getSeoClass($name);
        $className = self::getSeoHelper()->getSeoClass($name);

        if (!class_exists($className)) {
            Mage::throwException(sprintf(
                'Name "%s" doesn\'t have handler in "config/popov_seo/handlers" or class not found',
                $name
            ));
        }

        $type = self::getSeoHelper()->getSeoType($name);
        $rules = Mage::getModel('popov_seo/rule')->getCollection()
			->addFieldToFilter('type', $type)
			->addFieldToFilter('is_active', 1)
			->addFieldToFilter('store_id', array(
				array('finset' => 0), // all stores
				array('finset' => Mage::app()->getStore(true)->getId())
			))
			//->setOrder('created', 'DESC')
		;

		return new $className($rules);
	}

	public static function canCreate($name)
    {
        return class_exists(self::getSeoHelper()->getSeoClass($name));
    }

    /**
     * @return Popov_Seo_Helper_Data
     */
    protected static function getSeoHelper()
    {
        return Mage::helper('popov_seo');
    }

}
<?php
/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_<package>
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 16.10.14 12:28
 */

class Popov_Seo_Model_Seoblock extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('popov_seo/seoblock');
	}

	/**
	 * @param int $storeId
	 * @param int $categoryId
	 * @param string $url
	 * @param int $isActive
	 * @return Popov_Seo_Model_Resource_Seoblock_Collection
	 */
	public function getSeoText($storeId, $categoryId, $url, $isActive = 1)
	{
		/** @var Popov_Seo_Model_Resource_Seoblock_Collection $collection */
		$collection = $this->getCollection();
		$collection->addFieldToFilter('store_id', $storeId)
			->addFieldToFilter('category_id', $categoryId)
			->addFieldToFilter('url', $url)
			->addFieldToFilter('is_active', $isActive);

		return $collection;
	}

}
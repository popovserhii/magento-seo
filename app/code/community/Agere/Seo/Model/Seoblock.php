<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 16.10.14 12:28
 */

class Agere_Seo_Model_Seoblock extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('agere_seo/seoblock');
	}

	/**
	 * @param int $storeId
	 * @param int $categoryId
	 * @param string $url
	 * @param int $isActive
	 * @return Agere_Seo_Model_Resource_Seoblock_Collection
	 */
	public function getSeoText($storeId, $categoryId, $url, $isActive = 1)
	{
		/** @var Agere_Seo_Model_Resource_Seoblock_Collection $collection */
		$collection = $this->getCollection();
		$collection->addFieldToFilter('store_id', $storeId)
			->addFieldToFilter('category_id', $categoryId)
			->addFieldToFilter('url', $url)
			->addFieldToFilter('is_active', $isActive);

		return $collection;
	}

}
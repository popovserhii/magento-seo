<?php
/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_<package>
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 17.10.14 10:47
 */

class Popov_Seo_Model_System_Config_Category
{
	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		$helper = Mage::helper('popov_seo');

		$options = array('' => $helper->__('Choose category'));
		$categories = $this->getCategoriesValues(1);

		foreach ($categories as $category) {
			$options[$category['value']] =  $category['label'];
		}

		return $options;
	}

	/**
	 * @param int $parentId
	 * @return array
	 */
	public function getCategoriesValues($parentId)
	{
		/** @var Mage_Catalog_Model_Category $categoryModel */
		$categoryModel = Mage::getModel('catalog/category');
		/** @var Varien_Data_Tree_Node_Collection $categories */
		$tree = $categoryModel->getCategories($parentId);
		$node = null;

		foreach ($tree as $root) {
			$node = $root;
		}

		return ! is_null($node) ? $this->_buildCategoriesValues($node, array()) : array();
	}

	/**
	 * @param Varien_Data_Tree_Node $node
	 * @param array $values
	 * @param int $level
	 * @return array
	 */
	protected function _buildCategoriesValues(Varien_Data_Tree_Node $node, $values, $level = 0)
	{
		++ $level;

		$values[$node->getId()]['value'] = $node->getId();
		$values[$node->getId()]['label'] = str_repeat('--', $level).$node->getName();

		foreach ($node->getChildren() as $child) {
			$values = $this->_buildCategoriesValues($child, $values, $level);
		}

		return $values;
	}

}
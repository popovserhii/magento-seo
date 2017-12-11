<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 16.10.14 18:58
 */

class Popov_Seo_Block_Widget_Grid_Column_Renderer_Category extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render row store views
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
		$categoryNames = '';

		if ($row->getData('path'))
		{
			$pathIds = explode('/', $row->getData('path'));

			if ($pathIds[0] == 1)
			{
				unset($pathIds[0]);
			}

			$categoryModel = Mage::getSingleton('catalog/category');
			$categories = $categoryModel->getCollection()
				->addAttributeToSelect('name')
				->addAttributeToFilter('entity_id', array('in' => $pathIds))
				->load()
				->getItems();

			$tmp = [];

			foreach ($pathIds as $categoryId)
			{
				$tmp[] = $categories[$categoryId]->getName();
			}

			$categoryNames = implode('/', $tmp);
		}

		return $categoryNames;
    }

}

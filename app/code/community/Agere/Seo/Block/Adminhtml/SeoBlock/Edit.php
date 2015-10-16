<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 16.10.14 13:00
 */

class Agere_Seo_Block_Adminhtml_SeoBlock_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	protected function _construct()
	{
		$this->_blockGroup = 'agere_seo';
		$this->_controller = 'adminhtml_seoBlock';
	}

	public function getHeaderText()
	{
		$helper = Mage::helper('agere_seo');
		$model = Mage::registry('current_seoblock');

		if ($model->getId())
		{
			return $helper->__("Edit Seo Block item");
		}
		else
		{
			return $helper->__("Add Seo Block item");
		}
	}

}
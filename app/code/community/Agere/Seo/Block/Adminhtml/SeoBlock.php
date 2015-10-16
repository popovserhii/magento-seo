<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 16.10.14 12:53
 */

class Agere_Seo_Block_Adminhtml_SeoBlock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	protected function _construct()
	{
		parent::_construct();

		$helper = Mage::helper('agere_seo');
		$this->_blockGroup = 'agere_seo';
		$this->_controller = 'adminhtml_seoBlock';

		$this->_headerText = $helper->__('Seo Block');
		$this->_addButtonLabel = $helper->__('Add Seo Block');
	}

}
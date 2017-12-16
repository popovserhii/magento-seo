<?php
/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_<package>
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 16.10.14 12:53
 */

class Popov_Seo_Block_Adminhtml_SeoBlock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	protected function _construct()
	{
		parent::_construct();

		$helper = Mage::helper('popov_seo');
		$this->_blockGroup = 'popov_seo';
		$this->_controller = 'adminhtml_seoBlock';

		$this->_headerText = $helper->__('Seo Block');
		$this->_addButtonLabel = $helper->__('Add Seo Block');
	}

}
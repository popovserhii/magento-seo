<?php
/**
 * Block contains collection of robots.txt files
 *
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 26.09.15 13:18
 */
class Popov_Seo_Block_Adminhtml_Meta extends Mage_Adminhtml_Block_Widget_Grid_Container {

	/**
	 * @link http://stackoverflow.com/a/5716697/1335142
	 */
	protected function _construct() {
		$this->_blockGroup = 'popov_seo';
		$this->_controller = 'adminhtml_meta';
		$this->_headerText = $this->__('Meta Tags');
		$this->_addButtonLabel = $this->__('Add');

		parent::_construct();
	}

}
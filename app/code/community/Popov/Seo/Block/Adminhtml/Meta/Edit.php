<?php
/**
 * @category Agere
 * @package Popov_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 26.06.15 15:00
 */
class Popov_Seo_Block_Adminhtml_Meta_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	/**
	 * Init class
	 */
	protected function _construct() {
		$this->_blockGroup = 'popov_seo';
		$this->_controller = 'adminhtml_meta';

		$this->setData('action', $this->getUrl('*/*/save'));
	}

	/**
	 * Get Header text
	 *
	 * @return string
	 */
	public function getHeaderText() {
		$model = Mage::registry('current_agere_meta');
		if ($model->getId()) {
			return $this->__("Edit Rule");
		} else {
			return $this->__("Add Rule");
		}
	}

}
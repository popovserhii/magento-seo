<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 16.10.14 13:02
 */

class Agere_Seo_Block_Adminhtml_SeoBlock_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Load Wysiwyg on demand and Prepare layout
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();

		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled())
		{
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
	}

	protected function _prepareForm()
	{
		$helper = Mage::helper('agere_seo');
		$model = Mage::registry('current_seoblock');

		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/save', array(
				'id' => $this->getRequest()->getParam('id')
			)),
			'method' => 'post'
		));

		$this->setForm($form);

		$fieldset = $form->addFieldset('seoblock_form', array('legend' => $helper->__('Seo Block Information')));

		$fieldset->addField('store_id', 'select', array(
			'label'    => $helper->__('Store View'),
			'name'     => 'store_id',
			'required' => true,
			'value'    => $model->getStoreId(),
			'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(),
		));

		$fieldset->addField('category_id', 'select', array(
			'label'    => $helper->__('Category'),
			'name'     => 'category_id',
			'required' => true,
			'value'    => $model->getCategoryId(),
			'values'   =>  Mage::getModel('Agere_Seo_Model_System_Config_Category')->toOptionArray(),
		));

		$fieldset->addField('url', 'text', array(
			'label'				=> $helper->__('Url'),
			'required'			=> false,
			'name'				=> 'url',
			'after_element_html' => '<div class="hint"><p class="note">'.$this->__('Requires only part of the link without a store. For example: women/where/item-type/jeans').'</p></div>',
		));

		$fieldset->addField('is_active', 'select', array(
			'label'     => $helper->__('Status'),
			'name'      => 'is_active',
			'required'  => true,
			'options'   => array(
				'1' => $helper->__('Enabled'),
				'0' => $helper->__('Disabled'),
			),
		));

		if (! $model->getId())
		{
			$model->setData('is_active', '1');
		}

		$fieldset->addField('seo_text', 'editor', array(
			'label'     => $helper->__('Seo Text'),
			'name'      => 'seo_text',
			'required'  => true,
			'style'     => 'height: 25em;',
			'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'wysiwyg'	=> true
		));

		$form->setUseContainer(true);

		if ($data = Mage::getSingleton('adminhtml/session')->getFormData())
		{
			$form->setValues($data);
		}
		else
		{
			$form->setValues($model->getData());
		}

		return parent::_prepareForm();
	}

}
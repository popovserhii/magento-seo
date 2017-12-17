<?php
/**
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 26.06.15 15:14
 */
class Popov_Seo_Block_Adminhtml_Meta_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

	public function _construct() {
		parent::_construct();

		$this->setId('popov_meta_form'); //popov_seo_meta_form
		$this->setTitle($this->__('Meta Rule Information'));
	}

	/**
	 * Load Wysiwyg on demand and Prepare layout
	 */
	/*protected function _prepareLayout() {
		parent::_prepareLayout();
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}
	}*/

	protected function _prepareForm() {
		$model = Mage::registry('current_popov_meta');

		$form = new Varien_Data_Form(array(
			'id'     => 'edit_form',
			'action' => $this->getUrl('*/*/save', array(
				'id' => $this->getRequest()->getParam('id')
			)),
			'method' => 'post'
		));

		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend' => $this->__('Rule Information'),
			'class' => 'fieldset-wide',
		));

		if ($model->getId()) {
			$fieldset->addField('id', 'hidden', array(
				'name' => 'id',
			));
		}


		//Zend_Debug::dump(get_class(Mage::getSingleton('adminhtml/system_store'))); die(__METHOD__);
		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('store_id', 'multiselect', array(
				'name' => 'stores[]',
				'label' => $this->__('Store View'),
				'title' => $this->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));
		} else {
			$fieldset->addField('store_id', 'hidden', array(
				'name' => 'stores[]',
				'value' => Mage::app()->getStore(true)->getId(),
			));
		}
		
		/*$fieldset->addField('category_id', 'select', array(
			'label'    => $this->__('Category'),
			'name'     => 'category_id',
			'required' => true,
			'value'    => $model->getCategoryId(),
			'values'   => Mage::getModel('Popov_Seo_Model_System_Config_Category')->toOptionArray(),
		));*/

		$fieldset->addField('type', 'select', array(
			'label'     => $this->__('Type'),
			'name'      => 'type',
			'required'  => true,
			'options'   => array(
				'category' => $this->__('Category'),
				'product' => $this->__('Product'),
			),
		));

		$fieldset->addField('title', 'text', array(
			'label'              => $this->__('Meta Title'),
			'name'               => 'title',
			'required'           => false,
			'style'              => 'width:100%',
			'after_element_html' => '<small>Example: Buy {%category% %manufacturer%}:strtolower|translate - Kyiv, Ukraine. {%category% %manufacturer%}:translate online store {%website%}:ucfirst</small>',
		));

		$fieldset->addField('description', 'textarea', array(
			'label'              => $this->__('Meta Description'),
			'name'               => 'description',
			'required'           => false,
			'style'              => 'width:100%',
			'after_element_html' => '<small>Example: {%category% %manufacturer%}:strtolower|translate can order in our online store {%website%}:ucfirst. Large selection. Delivery in Ukraine. Call +38 066 555 5555</small>',
		));

		$fieldset->addField('keywords', 'text', array(
			'label'              => $this->__('Meta Keywords'),
			'name'               => 'keywords',
			'required'           => false,
			'style'              => 'width:100%',
			'after_element_html' => '<small>Example: {%category% %manufacturer%}:strtolower|translate, online store {%website%}:ucfirst</small>',
		));

        $fieldset->addField('h1', 'text', array(
            'label'              => $this->__('Content H1'),
            'name'               => 'h1',
            'required'           => false,
            'style'              => 'width:100%',
            'after_element_html' => '<small>Example: {%category% %manufacturer%}:strtolower|translate|ucfirst {%website%}:ucfirst</small>',
        ));

        $fieldset->addField('content', 'textarea', array(
            'name'     => 'content',
            'label'    => $this->__('Content Description'),
            'title'    => $this->__('Content Description'),
            'required' => false,
            'style'    => 'width: 100%; height: 200px;',
        ));

		$fieldset->addField('seo_attributes', 'text', array(
			'label'              => $this->__('Attributes'),
			'name'               => 'seo_attributes',
			'required'           => true,
			'style'              => 'width:100%',
			'after_element_html' => '<small>Example: category;manufacturer</small>',
		));

		$fieldset->addField('seo_option_filters', 'text', array(
			'label'              => $this->__('Attribute filters'),
			'name'               => 'seo_option_filters',
			'required'           => false,
			'style'              => 'width:100%',
			'after_element_html' => '<small>Example: category;manufacturer:2</small>',
		));

		$fieldset->addField('is_active', 'select', array(
			'label'     => $this->__('Status'),
			'name'      => 'is_active',
			'required'  => true,
			'options'   => array(
				1 => $this->__('Enabled'),
				0 => $this->__('Disabled'),
			),
		));

		$fieldset->addField('created_at', 'date', array(
			'label'    => $this->__('Created at'),
			'name'     => 'created_at',
			'required' => false,
			'readonly' => true,
			'disabled' => true,
			'format'   => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
			'time'     => true,
		));

		$fieldset->addField('updated_at', 'date', array(
			'label'    => $this->__('Updated at'),
			'name'     => 'updated_at',
			'required' => false,
			'readonly' => true,
			'disabled' => false,
			'format'   => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
			'time'     => true,
		));

		if ($data = Mage::getSingleton('adminhtml/session')->getFormData()) {
			$form->setValues($data);
		} else {
			$form->setValues($model->getData());
		}
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}

}
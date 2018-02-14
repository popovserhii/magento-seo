<?php
/**
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 26.06.15 15:14
 */
class Popov_Seo_Block_Adminhtml_Meta_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {
		$model = Mage::registry('current_popov_meta');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('base_fieldset', array(
			'legend' => $this->__('Rule Information'),
			'class' => 'fieldset-wide',
		));

		if ($model->getId()) {
			$fieldset->addField('id', 'hidden', array(
				'name' => 'id',
			));
		}

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
            $model->setStoreId(Mage::app()->getStore(true)->getId());
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

        $fieldset->addField('name', 'text', array(
            'label'              => $this->__('Name'),
            'name'               => 'name',
            'required'           => true,
            'style'              => 'width:100%',
            //'after_element_html' => '<small>Example: category;manufacturer</small>',
        ));

		$fieldset->addField('context', 'select', array(
			'label'     => $this->__('Context'),
			'name'      => 'context',
			'required'  => true,
			'options'   => Mage::getModel('Popov_Seo_Model_System_Config_Context')->toOptionArray(),
		));

        $fieldset->addField('content', 'textarea', array(
            'name'     => 'content',
            'label'    => $this->__('Content Rule'),
            'title'    => $this->__('Content Rule'),
            'required' => false,
            'style'    => 'width: 100%; height: 200px;',
            'after_element_html' => '<small>Example: Buy {%category% %manufacturer%}:strtolower|translate - Kyiv, Ukraine. {%category% %manufacturer%}:translate online store {%website%}:ucfirst</small>',
        ));

		$fieldset->addField('seo_attributes', 'text', array(
			'label'              => $this->__('Attributes'),
			'name'               => 'seo_attributes',
			'required'           => true,
			'style'              => 'width:100%',
			'after_element_html' => '<small>Example: category;manufacturer</small>',
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

		$fieldset->addField('priority', 'text', array(
			'label'     => $this->__('Priority'),
			'name'      => 'priority',
			'required'  => false,
            'style'     => 'width:20%',
            'after_element_html' => '<small>Higher priority means the rule is checked last. Preference is by latest rule. 
                By default, the first attached route is read.</small>',
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

		// this must be after fieldset declaration
        if ($data = Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues($data);
        } else {
            $form->setValues($model->getData());
        }
        #$form->setUseContainer(true);
        #$this->setForm($form);


        return parent::_prepareForm();
	}

}
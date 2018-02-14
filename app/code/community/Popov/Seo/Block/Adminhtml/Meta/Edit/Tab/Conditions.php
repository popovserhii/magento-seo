<?php
/**
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 26.06.15 15:14
 */
class Popov_Seo_Block_Adminhtml_Meta_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
    {
		$model = Mage::registry('current_popov_meta');

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_'); // is important, must be as begin of 'rule_conditions_fieldset'
        $this->setForm($form);
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend' => $this->__('Apply the rule only if the following conditions are met (leave blank for all order)'),
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        // this must be after fieldset declaration
        if ($data = Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues($data);
        } else {
            $form->setValues($model->getData());
        }

        return parent::_prepareForm();
	}

}
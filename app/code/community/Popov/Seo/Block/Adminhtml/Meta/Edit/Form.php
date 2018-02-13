<?php

/**
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 26.06.15 15:14
 */
class Popov_Seo_Block_Adminhtml_Meta_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function _construct()
    {
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

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', [
                'id' => $this->getRequest()->getParam('id'),
            ]),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
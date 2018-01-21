<?php
/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 21.01.2018 19:30
 */

class Popov_Seo_Model_System_Config_Context
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('popov_seo');
        $options = array(
            '' => $helper->__('Choose context'),

            'meta_title' => $helper->__('Meta Title'),
            'meta_description' => $helper->__('Meta Description'),
            'meta_keywords' => $helper->__('Meta Keywords'),
            'h1' => $helper->__('H1 Element'),
            'description' => $helper->__('Description Element'),
        );

        return $options;
    }
}
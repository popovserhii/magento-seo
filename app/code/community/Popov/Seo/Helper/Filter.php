<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2018 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

class Popov_Seo_Helper_Filter extends Mage_Core_Helper_Abstract
{
    protected $removeTrailingSlash;
    
    protected $hideDefaultStoreCode;
    
    
    public function allowRemoveTrailingSlash()
    {
        if (is_null($this->removeTrailingSlash)) {
            $this->removeTrailingSlash = Mage::getStoreConfig('popov_seo/settings/trailing_slash');
        }
        
        return $this->removeTrailingSlash;
    }
    
    /**
     * Should we remove default store code from URLs?
     *
     * @return bool
     */
    public function allowHideDefaultStoreCode()
    {
        if (is_null($this->hideDefaultStoreCode)) {
            $this->hideDefaultStoreCode = Mage::helper('core')->isModuleEnabled('Bubble_HideDefaultStoreCode')
                && Mage::helper('bubble_hdsc')->hideDefaultStoreCode();
        }

        return $this->hideDefaultStoreCode;
    }

    public function trailingSlash($href)
    {
        $href = rtrim($href, '/');

        // Check if fragment is in URL
        if ($pos = strpos($href, '#')) {
            $fragment = substr($href, $pos);
            $href = substr($href, 0, $pos);

            $href = rtrim($href, '/') . $fragment;
        }

        // Check if query presents in URL
        if ($pos = strpos($href, '?')) {
            $query = substr($href, $pos);
            $href = substr($href, 0, $pos);

            $href = rtrim($href, '/') . $query;
        }

        return $href;
    }

    /**
     * Remove default store code from url.
     *
     * Notice: Method works in tandem with Bubble_HideDefaultStoreCode module (@see https://github.com/popovserhii/magento-hide-default-store-code)
     * and only enhance that module capabilities.
     * Bubble_HideDefaultStoreCode module rewrites Mage_Core_Model_Store class, 
     * this potentially increases conflict with third-party modules.
     * This method guaranty if Bubble_HideDefaultStoreCode module has a conflict with other modules
     * all functionality will work correctly.
     *
     * @param $href
     * @return mixed
     */
    public function defaultStoreCode($href)
    {
        $store = Mage::app()->getDefaultStoreView();
        
        return str_replace('/' . $store->getCode() . '/', '/', $href);
    }
}
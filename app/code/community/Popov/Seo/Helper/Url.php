<?php
/**
 * Handle url
 *
 * @category Popov
 * @package Popov_Popov
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 24.07.15 16:23
 */

class Popov_Seo_Helper_Url extends Mage_Core_Helper_Abstract
{
    public function trimUrl($url)
    {
        return rtrim($url, '/');
    }
}
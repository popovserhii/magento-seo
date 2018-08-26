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
    public function trailingSlash($href)
    {
        $href = rtrim($href, '/');

        // Check if fragment is in URL
        if ($pos = strpos($href, '#')) {
            $fragment = substr($href, $pos);
            $href = substr($href, 0, $pos);

            $href = rtrim($href, '/') . $fragment;
        }

        // Check if query is in URL
        if ($pos = strpos($href, '?')) {
            $query = substr($href, $pos);
            $href = substr($href, 0, $pos);

            $href = rtrim($href, '/') . $query;
        }

        return $href;
    }
}
<?php
/**
 * @category Agere
 * @package Agere_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 27.06.15 23:47
 */
class Agere_Seo_Model_Url extends ManaPro_FilterSeoLinks_Model_Url {

	public function getUrl($routePath = null, $routeParams = null) {
		$url = parent::getUrl($routePath, $routeParams);

		return rtrim($url, '/');
	}

}
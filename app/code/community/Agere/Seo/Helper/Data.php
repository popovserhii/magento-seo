<?php
/**
 * SEO default helper
 *
 * @category Agere
 * @package Agere_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 20.04.14 14:54
 */ 
class Agere_Seo_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getSeoName() {
		static $seoName = '';

		/** @var Agere_Base_Helper_String $stringHelper */
		if (!$seoName) {
			$stringHelper = Mage::helper('agere_base/string');
			$moduleName = Mage::app()->getRequest()->getModuleName();
			$fullActionName = Mage::app()->getFrontController()->getAction()->getFullActionName();
			$actionName = str_replace($moduleName . '_', '', $fullActionName);
            $seoName = $stringHelper->create($actionName)->explode('_');
			$seoName = array_shift($seoName);
		}

		return $seoName;
	}

	public function hasFilters() {
		$filters = Mage::getSingleton('catalog/layer')->getState()->getFilters();

		return count($filters);
	}

	public function urlPageNormalize($url) {
		//$url = 'http://md-fashion.dev/ru/women/where/item-type/джинсы/p/1';
		$prepare = preg_replace('#^(.*)/p/1[\D]*$#', '$1', $url);
		$prepare = preg_replace('#/where$#', '', $prepare);

		//Zend_Debug::dump($s);
		//Zend_Debug::dump($prepare); die(__METHOD__);

		return $prepare;
	}

	/**
	 * Specific 301 redirect
	 *
	 * Here is 301 redirect which cannot place in .htaccess or analog file
	 */
	public function redirect301() {
		#RewriteRule     ^index.php/admin/(.*)$   	http://%{HTTP_HOST}/en/$1    	[R=301,NC,L]    # Permanent Move
		$list = array(
			'^/index.php/admin/(.*)$' => "http://{$_SERVER['HTTP_HOST']}/en/$1",
		);

		foreach ($list as $from => $to) {
			if (preg_match("#{$from}#", $_SERVER['REQUEST_URI'])) {
				$url = preg_replace("#{$from}#", $to, $_SERVER['REQUEST_URI']);

				header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
				header('Location: ' . rtrim($url, '/'));
				die();
			}
		}
	}

	public function redirectTrailingSlash() {
		if (Mage::getStoreConfig('agere_section/settings/trailing_slash')) {
			$currentUrl = Mage::helper('core/url')->getCurrentUrl();
			if ($_SERVER['REQUEST_METHOD'] != 'POST' && !Mage::app()->getRequest()->isAjax()) {
				/* if this need than check search
				 * if (strstr($currentUrl, '?')) {
					$url = strtok($currentUrl, '?');
				} else {
					$url = $currentUrl;
				}*/
				$url = $currentUrl;

				if ((substr($url, -1) == '/') && ($_SERVER['REQUEST_URI'] !== '/')) {
					header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
					header('Location: ' . rtrim($url, '/'));
					die();
				}
			}
		}
	}
	
	/**
	 * Redirect uppercase url to lowercase
	 */
	public function redirectToLowerCase() {
		if (Mage::getStoreConfig('agere_section/settings/to_lowercase') && $this->isCheckedModule()) {
			/** @var Mage_Core_Model_Url $url */
			$url = Mage::getModel('core/url');
			$url->parseUrl(Mage::helper('core/url')->getCurrentUrl());
			$path = rawurldecode($url->getPath());

			if ($path !== ($pathLower = mb_strtolower($path, 'UTF-8')) && !Mage::app()->getRequest()->isAjax()) {
				$urlNew = $url->getRebuiltUrl($pathLower);
				header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
				header('Location: ' . rtrim($urlNew, '/'));
				exit();
			}
		}
	}

	public function redirectMultipleSlashes() {
		if (Mage::getStoreConfig('agere_section/settings/multiple_slashes') && preg_match('#[/]{2,}#', $_SERVER['REQUEST_URI'])) {
			$parts = array_filter(explode('/', $_SERVER['REQUEST_URI']));
			$url = rtrim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), '/');
			
			foreach ($parts as $part) {
				$url .= '/' . rawurldecode($part);
			}

			header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
			header('Location: ' . $url);
			exit();
		}
	}

	/**
	 * If url cannot contain store than redirect to base store
	 *
	 * @return bool
	 */
	public function redirectWithoutStore() {
		if (Mage::getStoreConfig('web/url/use_store')
			&& $this->isCheckedModule()
			&& Mage::getStoreConfig('agere_section/settings/without_store')
			&& !Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {

			$requestUri = trim(rawurldecode($_SERVER['REQUEST_URI']), '/');
			$parts = explode('/', $requestUri);
			$storeDefault = Mage::app()->getStore(Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId());

			if (($storeDefault->getCode() !== trim($parts[0])) && ($storeDefault->getId() === Mage::app()->getStore()->getId())) {
				$url = Mage::getBaseUrl() . $requestUri;
				header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
				header('Location: ' . $url);
				exit();
			}
		}
	}

	public function redirectNonSecureUrl() {
		if (Mage::getStoreConfig('agere_section/settings/non_secure_url') && $this->isCheckedModule()) {
			$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
			if ($protocol === 'https') {
				$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
				header('HTTP/1.1 301 Moved Permanently');
				header('Location: http://' . $url);
				die();
			}
		}
	}

	public function redirectFirstPage() {
		if (Mage::getStoreConfig('agere_section/settings/first_page')) {
			$rawUrl = rtrim(rawurldecode($_SERVER['REQUEST_URI']), '/');
			
			if ($rawUrl !== ($url = $this->urlPageNormalize($rawUrl))) {
				header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
				header('Location: ' . $url);
				die();
			}

		}
	}

	public function addGoogleVerificationFor8080() {
	
		/** @var Mage_Page_Block_Html_Head $head */
		$head = Mage::app()->getLayout()->getBlock('head');
		$includes = $head->getIncludes();

		if ($code = Mage::getStoreConfig('agere_section/settings/google_site_verification')) {
			// md - IroTkpMFGodsAjsCBl4Bj7wMdV3JXXbE2xlw0rUfMic - google
			// oodji - gCEjRSIqf7vyjTPi5gYw83WPps2e9j92T-iVaLlZtss - google
			$includes .= '<meta name="google-site-verification" content="' . $code . '" />';
		}
		
		//Zend_Debug::dump($code); die(__METHOD__);

		if ($code = Mage::getStoreConfig('agere_section/settings/yandex_site_verification')) {
			// oodji - 744e216c8214dd51 - yandex
			$includes .= '<meta name="yandex-verification" content="' . $code . '" />';
		}
		
		$head->setData('includes', $includes);
	}

	public function prepareCanonicalLink() {
		if (Mage::getStoreConfig('agere_section/settings/canonical_link')) {
			/** @var Mage_Page_Block_Html_Head $head */
			$head = Mage::app()->getLayout()->getBlock('head');
			if ($head) {
				$head->addLinkRel('canonical', urldecode(Mage::helper('core/url')->getCurrentUrl()));
			}
		}
	}

	public function prepareHreflang() {
		if (Mage::getStoreConfig('agere_section/settings/rel_alternate_hreflang')) {
			/** @var Mage_Page_Block_Html_Head $head */
			$head = Mage::app()->getLayout()->getBlock('head');
			if ($head) {
				$head->addLinkRel('canonical', urldecode(Mage::helper('core/url')->getCurrentUrl()));

				/** @link http://stackoverflow.com/a/5867890 */

				//$isHomePage = Mage::getBlockSingleton('page/html_header')->getIsHomePage();
				//$storeCurr = Mage::app()->getStore();
				//$storeDefault = Mage::app()->getStore(Mage::app()->getWebsite(true)->getDefaultGroup()->getDefaultStoreId());
				$stores = Mage::app()->getWebsite()->getStores();

				$includes = '';
				/*foreach ($stores as $storeId => $store) {
					$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
					if (($storeCurr->getId() === $storeDefault->getId()) && ($storeCurr->getId() === $store->getId())) {
						if (!$isHomePage) {
							$baseUrl = $baseUrl . $store->getCode();
						}
						$includes .= str_replace('//', '/', sprintf($template, $store->getCode(), $baseUrl . $this->getUrlWithoutStore()));
						continue;
					} elseif ($storeCurr->getId() === $store->getId()) {
						continue;
					}
					$baseUrl = $baseUrl . $store->getCode();
					$includes .= sprintf($template, $store->getCode(), rtrim($baseUrl . $this->getUrlWithoutStore(), '/'));
				}*/

				$template = '<link rel="alternate" hreflang="%s" href="%s" />';
				$currentStoreId = Mage::app()->getStore()->getId();
				$useCountryByDefault = Mage::getStoreConfig('agere_section/settings/rel_alternate_hreflang_country');
				//Zend_Debug::dump($useCountryByDefault); die(__METHOD__);
				foreach ($stores as $store) {
					//if ($store->getId() != $currentStoreId) {
						Mage::app()->setCurrentStore($store->getId());
						$localeCode = Mage::getStoreConfig('general/locale/code', $store->getId());
						if ($useCountryByDefault) {
							$languageCode = substr(strtolower($localeCode), 0, 2);
							$countryCode = strtolower(Mage::getStoreConfig('general/country/default', $store));
							$hrefLang = $languageCode . '-' . $countryCode;
						} else {
							$hrefLang = strtolower(str_replace('_', '-', $localeCode));
						}
						$includes .= sprintf($template, $hrefLang, rtrim($store->getCurrentUrl(false), '/'));
					//}
				}
				Mage::app()->setCurrentStore($currentStoreId);

				$head->setData('includes', $head->getIncludes() . $includes);
			}
		}
	}

	/**
	 * Get url without store
	 *
	 * @return string
	 */
	public function getUrlWithoutStore() {
		$url = Mage::getSingleton('core/url')->parseUrl(urldecode(Mage::helper('core/url')->getCurrentUrl()));
		$parts = explode('/', trim($url->getPath(), '/'));

		if (isset($parts[0]) && in_array($parts[0], $this->getStoresParamsAs('code'))) {
			unset($parts[0]);
		}

		return '/' . implode('/', $parts);
	}

	public function getStoresParamsAs($parameter = 'id') {
		static $params = array();

		if (isset($params[$parameter])) {
			return $params[$parameter];
		}

		$method = 'get' . ucfirst($parameter);
		$stores = Mage::app()->getStores();
		foreach ($stores as $storeId => $store) {

			$params[$parameter][] = $store->{$method}();
		}

		return $params[$parameter];
	}

	/**
	 * @param string $url
	 * @return array
	 */
	public function parseUrl($url)
	{
		$params = array();
		$delimiter = '/';

		/** @var Mage_Core_Model_Url $core */
		$core = Mage::getSingleton('core/url');
		$core->parseUrl(urldecode($url));
		$requestUri = trim($core->getData('path'), $delimiter);
		$parseUri = explode($delimiter, $requestUri);

		if ($parseUri) {
			$params['storeId'] = Mage::app()->getStore($parseUri[0])->getId();
			$params['storeCode'] = Mage::app()->getStore($parseUri[0])->getCode();
			$whereKey = array_search('where', $parseUri);
			$pageKey = array_search('p', $parseUri);
			if ($pageKey !== false) {
				$params['page'] = (int) $parseUri[$pageKey + 1];
			}
			//$params['path'] = $this->_generatePath($pageKey, 0, $parseUri);
			$params['path'] = $this->_generatePath(0, 0, $parseUri);
			$params['urlPath'] = $this->_generatePath($whereKey, $pageKey, $parseUri);
			$category = Mage::getModel('catalog/category')->getCollection()
				->addAttributeToFilter('url_path', $params['urlPath'])->getFirstItem();
			$params['categoryId'] = $category->getId();
		}

		return $params;
	}

	public function isCheckedModule() {
		$secureList = array(
			'/catalogsearch/result',
			'/customer/account',
			'/checkout/cart',
			'/checkout/onepage',
			'/wishlist/index',
			'/autolux/index',
			'/sales/order',
			'/newsletter/manage',
			'/review/customer',
			'/misc/customer_account',
			'/magmi/index',
			'/acquiring/index',
			'/ebizautoresponder/autoresponder',
			'/robots/index'
		);

		$request = Mage::app()->getRequest();
		$key = '/' . $request->getModuleName() . '/' . $request->getControllerName();

		if (in_array($key, $secureList)) {
			return false;
		}

		return true;
	}
	
	/**
	 * @param int $firstKey
	 * @param int $secondKey
	 * @param string $uri
	 * @param int $offset
	 * @param string $delimiter
	 * @return string
	 */
	protected function _generatePath($firstKey, $secondKey, $uri, $offset = 1, $delimiter = '/') {
		$key = ($firstKey !== false) ? $firstKey : $secondKey;
		$key = $key ? $key - 1 : null;
		$pathPieces = array_slice($uri, $offset, $key);

		return implode($delimiter, $pathPieces);
	}
	
}

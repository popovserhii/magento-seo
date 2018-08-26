<?php
/**
 * SEO default helper
 *
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 20.04.14 14:54
 */ 
class Popov_Seo_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var Popov_Seo_Helper_Filter
     */
    protected $filterHelper;

    /**
     * @var Mage_Page_Block_Html_Head
     */
    protected $head;

    /**
     * @var Popov_Seo_Model_MetaTag_Abstract
     */
    protected $seoAdapter = null;

    protected $allowChangeCanonical = null;

    public function getSeoName()
    {
        static $seoName = null;

        if (is_null($seoName)) {
            $seoName = Mage::app()->getFrontController()->getAction()->getFullActionName();
        }

        return $seoName;
    }

    public function getSeoType($seoName = null)
    {
        static $seoType = null;

        if (is_null($seoType)) {
            $moduleName = Mage::app()->getRequest()->getModuleName();
            $fullActionName = $seoName ?: Mage::app()->getFrontController()->getAction()->getFullActionName();

            $actionName = str_replace($moduleName . '_', '', $fullActionName);
            $seoType = explode('_', $actionName);
            $seoType = array_shift($seoType);
        }

        return $seoType;
    }

    public function getSeoClass($seoName = null)
    {
        $seoName = $seoName ?: $this->getSeoName();
        $element = Mage::getConfig()->getNode('popov_seo/handlers/aliases/' . $seoName);
        $handler = $element ? $element->getAttribute('handler') : $seoName;
        $handlerClass = (string) Mage::getConfig()->getNode('popov_seo/handlers/' . $handler)->model;

        return $handlerClass;
    }

    public function getSeoAdapter()
    {
        if (is_null($this->seoAdapter)) {
            $this->seoAdapter = false;
            if (Popov_Seo_Model_MetaTag_Factory::canCreate($seoName = $this->getSeoName())) {
                $this->seoAdapter = Popov_Seo_Model_MetaTag_Factory::create($seoName);
            }
        }
        return $this->seoAdapter;
    }

    public function getHead()
    {
        if (!$this->head) {
            $this->head = Mage::app()->getLayout()->getBlock('head');
        }

        return $this->head;
    }

    public function hasFilters()
    {
		$filters = Mage::getSingleton('catalog/layer')->getState()->getFilters();

		return count($filters);
	}

    public function allowChangeCanonical()
    {
        if (is_null($this->allowChangeCanonical)) {
            $allow = false;
            if (Mage::getStoreConfig('popov_seo/settings/canonical')) {
                $allow = true;
                $part = explode("\n", Mage::getStoreConfig('popov_seo/settings/canonical_exclude_pages'));
                foreach ($part as $page) {
                    if ($this->getSeoName() === trim($page)) {
                        $allow = false;
                    }
                }
            }
            $this->allowChangeCanonical = $allow;
        }

        return $this->allowChangeCanonical;
    }

    /**
     * Get pager block
     *
     * @return Mage_Page_Block_Html_Pager
     */
    public function getToolbar()
    {
        static $tool;

        if (!$tool) {
            $tool = Mage::app()
                ->getLayout()
                ->createBlock('page/html_pager')
                ->setLimit(Mage::app()->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit());
        }

        return $tool;
    }

    public function getPage()
    {
        return (int) $this->getToolbar()->getRequest()->getParam($this->getToolbar()->getPageVarName());
    }

    public function urlPageNormalize($url)
    {
		//$url = 'http://example.com/ru/women/where/item-type/джинсы/p/1';
		$prepare = preg_replace('#^(.*)/p/1[\D]*$#', '$1', $url);

		return $prepare;
	}

	/**
	 * Specific 301 redirect
	 *
	 * Here is 301 redirect which cannot place in .htaccess or analog file
	 */
	public function redirect301()
    {
		#RewriteRule     ^index.php/admin/(.*)$   	http://%{HTTP_HOST}/en/$1    	[R=301,NC,L]    # Permanent Move
		$list = array(
			//'^/index.php/admin/(.*)$' => "http://{$_SERVER['HTTP_HOST']}/en/$1",
			//'(.*)/mode/list$' => "http://{$_SERVER['HTTP_HOST']}/$1",
			//'(.*)/mode/grid$' => "http://{$_SERVER['HTTP_HOST']}/$1",
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


	public function redirectIndexPhp()
    {
		if (Mage::getStoreConfig('popov_seo/settings/index_php')
            && Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_USE_REWRITES)
            && !Mage::app()->getRequest()->isAjax()
        ) {
            $currentUrl = Mage::helper('core/url')->getCurrentUrl();
            if (strpos($currentUrl, 'index.php/') !== false) {
                $pos = strpos($currentUrl, 'index.php/');
                $p1 = substr($currentUrl, 0, $pos);
                $p2 = substr($currentUrl, $pos + 10, strlen($currentUrl));
                $url = $p1 . $p2;

                header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
                header('Location: ' . rtrim($url, '/'));
                die();
            }
		}
	}

	public function redirectTrailingSlash()
    {
		if (Mage::getStoreConfig('popov_seo/settings/trailing_slash') && $this->isCheckedModule()) {
			$currentUrl = Mage::helper('core/url')->getCurrentUrl();
			if ($_SERVER['REQUEST_METHOD'] != 'POST' && !Mage::app()->getRequest()->isAjax()) {
				$url = $currentUrl;

				if ((substr($url, -1) == '/') && ($_SERVER['REQUEST_URI'] !== '/')) {
                    $filter = $this->getFilterHelper();

					header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
					header('Location: ' . $filter->trailingSlash($url));
					die();
				}
			}
		}
	}

	/**
	 * Redirect uppercase url to lowercase
	 */
	public function redirectToLowerCase()
    {
		if (Mage::getStoreConfig('popov_seo/settings/to_lowercase') && $this->isCheckedModule()) {
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

	public function redirectMultipleSlashes()
    {
		if (Mage::getStoreConfig('popov_seo/settings/multiple_slashes')
            && preg_match('#[/]{2,}#', $_SERVER['REQUEST_URI'])) {
			$parts = array_filter(explode('/', $_SERVER['REQUEST_URI']));
			$url = rtrim(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB), '/');
			
			foreach ($parts as $part) {
				$url .= '/' . rawurldecode($part);
			}

			header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
			header('Location: ' . $url);
			die();
		}
	}

	/**
	 * If url cannot contain store than redirect to base store
	 *
	 * @return bool
	 */
	public function redirectWithoutStore()
    {
		if (Mage::getStoreConfig('web/url/use_store')
			&& $this->isCheckedModule()
			&& Mage::getStoreConfig('popov_seo/settings/without_store')
			//&& !Mage::getBlockSingleton('page/html_header')->getIsHomePage()) {
			&& !$this->isHomePage()) {
			
			$requestUri = trim(rawurldecode($_SERVER['REQUEST_URI']), '/');
			$parts = explode('/', $requestUri);
			$storeDefault = Mage::app()->getStore(Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId());

			if (($storeDefault->getCode() !== trim($parts[0])) && ($storeDefault->getId() === Mage::app()->getStore()->getId())) {
				$url = Mage::getBaseUrl() . $requestUri;
				header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
				header('Location: ' . $url);
				die();
			}
		}
	}

	public function redirectNonSecureUrl()
    {
		if (Mage::getStoreConfig('popov_seo/settings/non_secure_url') && $this->isCheckedModule()) {
			$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443)
                ? 'https'
                : 'http';
			if ($protocol === 'https') {
				$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
                header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
				header('Location: http://' . $url);
				die();
			}
		}
	}

	public function redirectFirstPage()
    {
		if (Mage::getStoreConfig('popov_seo/settings/first_page')) {
			$rawUrl = rtrim(rawurldecode($_SERVER['REQUEST_URI']), '/');
			if ($rawUrl !== ($url = $this->urlPageNormalize($rawUrl))) {
				header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
				header('Location: ' . $url);
				die();
			}
		}
	}

	public function addSiteVerification()
    {
        $head = $this->getHead();
		$includes = $head->getIncludes();

		if ($code = Mage::getStoreConfig('popov_seo/settings/google_site_verification')) {
			$includes .= '<meta name="google-site-verification" content="' . $code . '" />';
		}
		
		if ($code = Mage::getStoreConfig('popov_seo/settings/yandex_site_verification')) {
			$includes .= '<meta name="yandex-verification" content="' . $code . '" />';
		}
		
		$head->setData('includes', $includes);
	}

    public function prepareMetaTags()
    {
        if ($seoAdapter = $this->getSeoAdapter()) {
            $seoAdapter->run();
        }
    }

    /**
     * If there is none registered Seo Class than change canonical meta tag by default,
     * otherwise use appropriate adapter
     */
	public function prepareCanonical()
    {
        if ($this->allowChangeCanonical() && ($head = $this->getHead())) {
            if (($seoAdapter = $this->getSeoAdapter()) && method_exists($seoAdapter, 'prepareCanonical')) {
                $url = $seoAdapter->prepareCanonical();
            }
            if (empty($url)) {
                $url = urldecode(Mage::helper('core/url')->getCurrentUrl());
            }

            $url = Mage::getModel('core/url')->sessionUrlVar($url);

            $head->addLinkRel('canonical', $url);
        }
	}

	public function prepareHreflang()
    {
		if (Mage::getStoreConfig('popov_seo/settings/rel_alternate_hreflang')) {
            if ($head = $this->getHead()) {
				/** @link http://stackoverflow.com/a/5867890 */
				$stores = Mage::app()->getWebsite()->getStores();
				$includes = '';
				$template = '<link rel="alternate" hreflang="%s" href="%s" />';
				$currentStoreId = Mage::app()->getStore()->getId();
				foreach ($stores as $store) {
                    Mage::app()->setCurrentStore($store->getId());
                    $hrefLang = $this->getHrefLang($store);
                    $includes .= sprintf($template, $hrefLang, rtrim($store->getCurrentUrl(false), '/'));
				}
				Mage::app()->setCurrentStore($currentStoreId);

				$head->setData('includes', $head->getIncludes() . $includes);
			}
		}
	}

    public function getHrefLang($store = null) {
        if (!$store) {
            $store = Mage::app()->getStore();
        }

        $useCountryByDefault = Mage::getStoreConfig('popov_seo/settings/rel_alternate_hreflang_country');
        $localeCode = Mage::getStoreConfig('general/locale/code', $store->getId());
        if ($useCountryByDefault) {
            $languageCode = substr(strtolower($localeCode), 0, 2);
            $countryCode = strtolower(Mage::getStoreConfig('general/country/default', $store));
            $hrefLang = $languageCode . '-' . $countryCode;
        } else {
            $hrefLang = strtolower(str_replace('_', '-', $localeCode));
        }

        return $hrefLang;
    }

	public function prepareStaticNoindexNofollow()
    {
        if ($head = $this->getHead()) {
			$staticList = array(
				'/checkout/cart',
				'/customer/account',
				'/contacts/index',
				'/catalogsearch/result',
			);

			$request = Mage::app()->getRequest();
			$key = '/' . $request->getModuleName() . '/' . $request->getControllerName();

			if (in_array($key, $staticList)) {
				$head->setRobots('NOINDEX,NOFOLLOW');
			}
		}
	}

    public function prepareLinks($response)
    {
        if (Mage::app()->getRequest()->isAjax()) {
            return;
        }

        $filter = $this->getFilterHelper();

        if (Mage::getStoreConfig('popov_seo/settings/trailing_slash')) {
            $regexp = "/<a(.*?)href=\"(.*?)\"(.*?)>/";
            $html = preg_replace_callback($regexp, function ($matches) use ($filter) {
                list($full, $attrsBefore, $href, $attrsAfter) = $matches;
                // There is no needs add all filters for lower case URLs, multiple slashes and etc.
                // You can but more exceptable way will be fix it in root of problem,
                // such as change URL with upper case simbols through admin panel
                $href = $filter->trailingSlash($href);
                $href = sprintf('<a%shref="%s"%s>', $attrsBefore, $href, $attrsAfter);

                return $href;
            }, $response->getBody());

            $response->setBody($html);
        }

    }

	/**
	 * Get url without store
	 *
	 * @return string
	 */
	public function getUrlWithoutStore()
    {
		$url = Mage::getSingleton('core/url')->parseUrl(urldecode(Mage::helper('core/url')->getCurrentUrl()));
		$parts = explode('/', trim($url->getPath(), '/'));

		if (isset($parts[0]) && in_array($parts[0], $this->getStoresParamsAs('code'))) {
			unset($parts[0]);
		}

		return '/' . implode('/', $parts);
	}

	public function getStoresParamsAs($parameter = 'id')
    {
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
			'/robots/index',
			'/novaposhta/index',
			'/newsletter/subscriber',
            //'wayforpay_payment_redirect_index'
            '/WayForPay/redirect',
            '/WayForPay/response',
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
	
	public function isHomePage() {
		//$routeName = Mage::app()->getRequest()->getRouteName();
		//$identifier = Mage::getSingleton('cms/page')->getIdentifier();
		//return ($routeName == 'cms' && $identifier == 'home') ? true : false;
		$storeDefault = Mage::app()->getStore(Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId());
		$baseUrl = Mage::getBaseUrl();
		$currentUrl = Mage::helper('core/url')->getCurrentUrl() . $storeDefault->getCode();

		//ob_start();
		//Zend_Debug::dump($baseUrl);
		//Zend_Debug::dump($currentUrl);
		//$items .= ob_get_clean();
		//echo '<div class="popov-dump" style="display:none">' . $items . '</div>';
		
		return (trim($baseUrl, '/') === trim($currentUrl, '/')) ? true : false;
	}

    /**
     * @return Popov_Seo_Helper_Filter
     */
	protected function getFilterHelper()
    {
        if (!$this->filterHelper) {
            $this->filterHelper = $filter = Mage::helper('popov_seo/filter');
        }

        return $this->filterHelper;
    }
}

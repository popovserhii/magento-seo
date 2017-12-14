<?php
/**
 * Category SEO meta tags
 *
 * @category Popov
 * @package Popov_Seo
 * @author Popov Sergiy <popov@popov.com.ua>
 * @datetime: 20.04.14 19:31
 */
class Popov_Seo_Model_MetaTag_Category extends Popov_Seo_Model_MetaTag_Abstract {

	protected $filterDelimeter = ', ';

	protected function preRun() {
		$this->prepareLinkRel();
	}

	protected function postRun() {
		// unset current category description
		//if (Mage::helper('popov_seo')->hasFilters()) {
		//	$currentCategory = Mage::registry('current_category');
		//	$currentCategory->setData('description', '');
		//}

		//$this->changeH1TitleTag();

		$this->noindex('catalog/layer');
		$this->index('catalog/layer');

		$this->order();
        $this->attachHandler();

		parent::postRun();
	}

	protected function attachHandler()
    {
        if ($this->getFittingRule()) {
            /** @var Mage_Catalog_Helper_Output $outputHelper */
            $outputHelper = Mage::helper('catalog/output');
            $outputHelper->addHandler('categoryAttribute', $this);
        }
    }

    public function categoryAttribute(Mage_Catalog_Helper_Output $outputHelper, $outputHtml, $params)
    {
        //$category = $params['category'];
        if (($params['attribute'] === 'name')) {
            $outputHtml = $this->modifyContent('h1', $outputHtml);
        } elseif ($params['attribute'] === 'description') {
            $outputHtml = $this->modifyContent('description', $outputHtml);
        }

        return $outputHtml;
    }

	/*protected function convertH1() {
		if (Mage::getStoreConfig('popov_section/settings/dynamic_title')) {
			$fittingAttrs = $this->getFittingFilterAttributes()['value'];
			$bestRule = $this->getFittingRule();
			//Zend_Debug::dump($fittingAttrs); die(__METHOD__);
			if (!$bestRule) {
				return false;
			}

			$tagValue = trim($this->prepareValue($bestRule->getData('h1_title'), $this->prepareAttrs($fittingAttrs)));

		}

		return false;
	}*/

	protected function modifyContent($name, $outputHtml)
    {
        if (Mage::getStoreConfig('popov_section/settings/dynamic_' . $name)) {
            $fittingAttrs = $this->getFittingFilterAttributes()['value'];
            $bestRule = $this->getFittingRule();
            $outputHtml = trim($this->prepareValue($bestRule->getData($name), $this->prepareAttrs($fittingAttrs)));
        }

        return $outputHtml;
    }

	protected function noindex($layerModel) {
		/** @var $head Mage_Page_Block_Html_Head */
		if (($head = Mage::getSingleton('core/layout')->getBlock('head'))) {
			$noIndex = false;
			$follow = false;

			/** @var $layer Mage_Catalog_Model_Layer */
			//$layer = Mage::getSingleton($layerModel);
			foreach (explode(',', Mage::getStoreConfig('popov_section/settings/no_index')) as $noIndexProcessorName) {
				if (!$noIndexProcessorName) {
					continue;
				}

				$noIndexProcessor = Mage::getModel((string)Mage::getConfig()->getNode('popov_seo_filterlinks/noindex')->$noIndexProcessorName->model);
				if ($noIndexProcessor->detect($layerModel)) {
					$noIndex = true;
					break;
				}
			}

			foreach (explode(',', Mage::getStoreConfig('popov_section/settings/follow')) as $followProcessorName) {
				if (!$followProcessorName) {
					continue;
				}

				$followProcessor = Mage::getModel((string)Mage::getConfig()->getNode('popov_seo_filterlinks/noindex')->$followProcessorName->model);
				if ($followProcessor->detect($layerModel)) {
					$follow = true;
					break;
				}
			}

			if ($noIndex) {
				$head->setRobots($follow ? 'NOINDEX, FOLLOW' : 'NOINDEX, NOFOLLOW');
			}
		}
	}

	/**
	 * Override noindex data if found suitable rule
	 *
	 * @param string $layerModel
	 */
	protected function index($layerModel) {
		/** @var $head Mage_Page_Block_Html_Head */
		if (($head = Mage::getSingleton('core/layout')->getBlock('head'))) {
			$index = false;

			//Zend_Debug::dump(Mage::getStoreConfig('popov_section/settings/index', Mage::app()->getStore())); //die(__METHOD__);
			
			/** @var $layer Mage_Catalog_Model_Layer */
			foreach (explode(',', Mage::getStoreConfig('popov_section/settings/index')) as $indexProcessorName) {
				if (!$indexProcessorName) {
					continue;
				}

				$indexProcessorConfig = Mage::getConfig()->getNode('popov_seo_filterlinks/index')->$indexProcessorName;
				$indexProcessor = Mage::getModel((string) $indexProcessorConfig->model);
				
				if ($indexProcessor->detect($layerModel, $indexProcessorConfig->rule)) {
					$index = true;
					break;
				}
			}

			if ($index) {
				$head->setRobots('INDEX, FOLLOW');
			}
		}
	}

	protected function order() {
		/** @var Mage_Page_Block_Html_Head $head */
		$head = Mage::app()->getLayout()->getBlock('head');
		if ($head) {
			/** @var Mage_Catalog_Block_Product_List_Toolbar $toolbar */
			$toolbar = Mage::getBlockSingleton('catalog/product_list_toolbar');

			$request = Mage::app()->getRequest();
			$limit = $request->getParam($toolbar->getLimitVarName());
			$order = $request->getParam($toolbar->getOrderVarName());
			$direction = $request->getParam($toolbar->getDirectionVarName());

			if ($order || $direction || $limit) {
				$head->setRobots('NOINDEX, NOFOLLOW');
			}
		}
	}

	/**
	 * Get pager block
	 *
	 * @return Mage_Page_Block_Html_Pager $tool
	 */
	private function getToolbar() {
		static $tool;

		if (!$tool) {
			$category = Mage::registry('current_category');
			$prodCol = $category->getProductCollection()->addAttributeToFilter('status', 1)->addAttributeToFilter('visibility', array('in' => array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)));
			$tool = Mage::app()->getLayout()->createBlock('page/html_pager')->setLimit(Mage::app()->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit())->setCollection($prodCol);
		}

		return $tool;
	}

	protected function prepareLinkRel() {
		if (Mage::getStoreConfig('popov_section/settings/rel_prev_next')) {
			/** @var Mage_Page_Block_Html_Head $head */
			$head = Mage::app()->getLayout()->getBlock('head');
			if ($head) {
				$tool = $this->getToolbar();

				$linkPrev = false;
				$linkNext = false;
				if ($tool->getCollection()->getSize()) {
					if ($tool->getLastPageNum() > 1) {
						if (!$tool->isFirstPage()) {
							$linkPrev = true;
							if ($tool->getCurrentPage() == 2) {
								$url = explode('?', $tool->getPreviousPageUrl());
								$prevUrl = isset($url[0]) ? $url[0] : '';
							}
							else {
								$prevUrl = $tool->getPreviousPageUrl();
							}
						}
						if (!$tool->isLastPage()) {
							$linkNext = true;
							$nextUrl = $tool->getNextPageUrl();
						}
					}
				}

				if ($linkPrev) {
					$head->addLinkRel('prev', Mage::helper('popov_seo')->urlPageNormalize($prevUrl));
				}
				if ($linkNext) {
					$head->addLinkRel('next', $nextUrl);
				}
			}
		}
	}

	/**
	 * Get fitting filters
	 *
	 * @return array
	 */
	public function getFittingFilterAttributes() {
		static $fitting = null;

		if (is_null($fitting)) {
			$seoAttr = $this->registerSeoFilter('category');
			$fitting['id'][$seoAttr] = Mage::registry('current_category')->getid();
			$fitting['value'][$seoAttr] = trim(Mage::registry('current_category')->getName());
			if (($currentPage = $this->getToolbar()->getCurrentPage()) > 1) {
				$seoAttr = $this->registerSeoFilter('page');
				$fitting['id'][$seoAttr] = $currentPage;
				$fitting['value'][$seoAttr] = $currentPage;
			}

			/** @var Mage_Catalog_Model_Layer_Filter_Item $filter */
			/** @var Mage_Eav_Model_Entity_Attribute_Frontend_Default $attrFront */
			$filters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
			foreach ($filters as $filter) {
				$attrFront = $filter->getFilter()->getAttributeModel()->getFrontend();
				$attr = $attrFront->getAttribute();

				//if ($seoAttr = $this->registerSeoFilter($attr->getAttributeCode())) {
				$seoAttr = $this->registerSeoFilter($attr->getAttributeCode());
				$id = $attr->getSource()->getOptionId($filter->getValue());
				$value = $attrFront->getOption($filter->getValue());

				//$fitting['id'][$seoAttr] = isset($fitting['id'][$seoAttr]) ? $fitting['id'][$seoAttr] . $this->filterDelimeter . $id : $id;
				//$fitting['value'][$seoAttr] = isset($fitting['value'][$seoAttr]) ? $fitting['value'][$seoAttr] . $this->filterDelimeter . $value : $value;
				$fitting['id'][$seoAttr][] = $id;
				$fitting['value'][$seoAttr][] = $value;
				//}
			}

			//Zend_Debug::dump($fitting); //die(__METHOD__);

		}

		return $fitting;
	}

	protected function getAdditionalValues() {
		$values = parent::getAdditionalValues();
		$values['categories'] = implode(' ', $this->getCategoryPathNames());

		return $values;
	}

	public function getRules() {
		static $filterSet = false;

		if (!$filterSet) {
			$attrs = array_keys($this->getFittingFilterAttributes()['id']);
			$originAttrs = $this->handleSeoAttributes($attrs);
			$this->rules->addFieldToFilter('seo_attributes', implode(';', $originAttrs));

			$filterSet = true;
		}

		return $this->rules;
	}

	/**
	 * @return bool|string
	 * @deprecated
	 */
	/*protected function getGeneralEndingOfMetaTags() {
		static $end = null;

		if (is_null($end)) {
			$end = false;
			if (Mage::getStoreConfig('popov_section/settings/pager')) {
				$tool = $this->getToolbar();
				if ($this->isPageInUrl()) {
					if ($tool->getLastPageNum() > 1) {
						$end = Mage::helper('popov_seo')->__('page') . ' ' . $tool->getCurrentPage();
					}
				}
			}
		}

		return $end;
	}*/

	private function isPageInUrl() {
		$tool = $this->getToolbar();
		return ((int) $tool->getRequest()->getParam($tool->getPageVarName())) && $tool->getCollection()->getSize();
	}

	public function getCategoryPathNames() {
		$category = Mage::getModel('catalog/category')->load(Mage::registry('current_category')->getId());
		$coll = $category->getResourceCollection();
		$pathIds = array_slice($category->getPathIds(), 2);
		$coll->addAttributeToSelect('name');
		$coll->addAttributeToFilter('entity_id', array('in' => $pathIds));
		$categories = array();
		foreach ($coll as $cat) {
			$categories[] = trim($cat->getName());
		}

		return $categories;
	}


}
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

	protected $isHandlerAttached = false;

	protected function preRun()
    {
		$this->prepareLinkRel();
		//$this->prepareCanonical();
	}

	protected function postRun()
    {
		$this->noindex('catalog/layer');
		$this->index('catalog/layer');

		$this->order();

		$this->unsetDescription();

		parent::postRun();
	}

	protected function noindex($layerModel)
    {
		/** @var $head Mage_Page_Block_Html_Head */
		if (($head = Mage::getSingleton('core/layout')->getBlock('head'))) {
			$noIndex = false;
			$follow = false;

			/** @var $layer Mage_Catalog_Model_Layer */
			//$layer = Mage::getSingleton($layerModel);
			foreach (explode(',', Mage::getStoreConfig('popov_seo/settings/no_index')) as $noIndexProcessorName) {
				if (!$noIndexProcessorName) {
					continue;
				}

				$noIndexProcessor = Mage::getModel((string)Mage::getConfig()->getNode('popov_seo_filterlinks/noindex')->{$noIndexProcessorName}->model);
				if ($noIndexProcessor->detect($layerModel)) {
					$noIndex = true;
					break;
				}
			}

			foreach (explode(',', Mage::getStoreConfig('popov_seo/settings/follow')) as $followProcessorName) {
				if (!$followProcessorName) {
					continue;
				}

				$followProcessor = Mage::getModel((string)Mage::getConfig()->getNode('popov_seo_filterlinks/noindex')->{$followProcessorName}->model);
				if ($followProcessor->detect($layerModel)) {
					$follow = true;
					break;
				}
			}

			if ($noIndex) {
				$head->setRobots($follow ? 'NOINDEX,FOLLOW' : 'NOINDEX,NOFOLLOW');
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

			//Zend_Debug::dump(Mage::getStoreConfig('popov_seo/settings/index', Mage::app()->getStore())); //die(__METHOD__);
			
			/** @var $layer Mage_Catalog_Model_Layer */
			foreach (explode(',', Mage::getStoreConfig('popov_seo/settings/index')) as $indexProcessorName) {
				if (!$indexProcessorName) {
					continue;
				}

				$indexProcessorConfig = Mage::getConfig()->getNode('popov_seo_filterlinks/index')->{$indexProcessorName};
				$indexProcessor = Mage::getModel((string) $indexProcessorConfig->model);
				
				if ($indexProcessor->detect($layerModel, $indexProcessorConfig->rule)) {
					$index = true;
					break;
				}
			}

			if ($index) {
				$head->setRobots('INDEX,FOLLOW');
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
				$head->setRobots('NOINDEX,NOFOLLOW');
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

		if (!$tool && ($category = Mage::registry('current_category'))) {
		    /** @var Popov_Seo_Helper_Data $seoHelper */
		    $seoHelper = Mage::helper('popov_seo');
            $filteredList = Mage::app()->getLayout()
                ->getBlockSingleton('catalog/product_list')
                ->getLoadedProductCollection();

			//$prodCol = $category->getProductCollection()
            //    ->addAttributeToFilter('status', 1)
            //    ->addAttributeToFilter('visibility', array('in' => array(Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG, Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)));
			//$tool = Mage::app()->getLayout()->createBlock('page/html_pager')->setLimit(Mage::app()->getLayout()->createBlock('catalog/product_list_toolbar')->getLimit())->setCollection($prodCol);

            $tool = $seoHelper->getToolbar()->setCollection($filteredList);
		}

		return $tool;
	}

    public function prepareCanonical()
    {
        if (($category = Mage::registry('current_category'))) {
            $url = $category->getUrlModel()->getCategoryUrl($category);

            return $url;
        }
    }

	protected function prepareLinkRel()
    {
		if (Mage::getStoreConfig('popov_seo/settings/rel_prev_next')) {
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
					//$head->addLinkRel('prev', Mage::helper('popov_seo')->urlPageNormalize($prevUrl));
					$head->addLinkRel('prev', $prevUrl);
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
		    if ($category = Mage::registry('current_category')) {
                $seoAttr = $this->registerSeoFilter('category');
                $fitting['id'][$seoAttr] = $category->getId();
                $fitting['value'][$seoAttr] = trim($category->getName());
            }
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

	protected function getAdditionalValues()
    {
		$values = parent::getAdditionalValues();
        if ($category = Mage::registry('current_category')) {
            $values['category_ids'] = $category->getId();
            $values['categories'] = implode(' ', $this->getCategoryPathNames());
        }

		return $values;
	}

	public function getRules()
    {
		static $filterSet = false;

		if (!$filterSet) {
			$attrs = array_keys($this->getFittingFilterAttributes()['id']);
			$originAttrs = $this->handleSeoAttributes($attrs);

            // add category as default value
            $this->rules->addFieldToFilter('seo_attributes', array('in' => array('category', implode(';', $originAttrs))));

			$filterSet = true;
		}

		return $this->rules;
	}

	private function getPage()
    {
	    return (int) $this->getToolbar()->getRequest()->getParam($this->getToolbar()->getPageVarName());
    }


	public function getCategoryPathNames()
    {
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

    protected function changeH1($value, $tag = null)
    {
        $this->attachHandler();
    }

    protected function changeDescription($value, $tag = null)
    {
        $this->attachHandler();
    }

    protected function attachHandler()
    {
        if (/*$this->getFittingRules() && */!$this->isHandlerAttached) {
            $outputHelper = Mage::helper('catalog/output');
            $outputHelper->addHandler('categoryAttribute', $this);
            $this->isHandlerAttached = true;
        }
    }

    public function categoryAttribute(Mage_Catalog_Helper_Output $outputHelper, $outputHtml, $params)
    {
        $tagMaps = [
            'name' => 'h1',
            'description' => 'description',
        ];

        $tagName = $tagMaps[$params['attribute']];
        if (!isset($this->tags[$tagName]) || !$this->tags[$tagName]) {
            return $outputHtml;
        }

        if ($tagName === 'h1') {
            $outputHtml = $this->tags['h1'];
        } elseif ($tagName === 'description') {
            if (Mage::getStoreConfig('popov_seo/settings/description_on_first_page') && ($this->getPage() > 1)) {
                $outputHtml = '';
            } else {
                $outputHtml = $this->tags['description'];
            }
        }

        return $outputHtml;
    }

    protected function unsetDescription()
    {
        $category = Mage::getModel('catalog/layer')->getCurrentCategory();
        if (!$category) {
            return;
        }

        if ($this->allowUnsetDescription()) {
            $category->setDescription('');

            return;
        }
    }

    /**
     * There is check in category/view.phtml if description is false then OutputHelper isn't calling.
     *
     * This behavior unset default category description if below condition is true
     * and there is no rule found
     */
    protected function allowUnsetDescription()
    {
        $filters = Mage::getSingleton('catalog/layer')->getState()->getFilters();

        return Mage::getStoreConfig('popov_seo/settings/description_on_first_page')
            && (($this->getPage() > 1) || (count($filters)) && !isset($this->tags['description']));
    }
}
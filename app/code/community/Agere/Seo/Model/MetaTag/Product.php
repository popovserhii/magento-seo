<?php
/**
 * Product SEO meta tags
 *
 * @category Agere
 * @package Agere_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 20.04.14 19:31
 */
class Agere_Seo_Model_MetaTag_Product extends Agere_Seo_Model_MetaTag_Abstract {

	/**
	 * Get fitting filters
	 *
	 * @return array
	 */
	public function getFittingFilterAttributes() {
		static $fitting = array();


		/*$fitting['id'][$seoAttr] = Mage::registry('current_category')->getid();
		$fitting['value'][$seoAttr] = Mage::registry('current_category')->getName();*/


		if (!$fitting) {
			$product = Mage::registry('current_product');
			$rule = $this->getRules()->getFirstItem();
			$seoAttributes = explode(';', $rule->getSeoAttributes());
			foreach ($seoAttributes as $seoAttr) {
				if ($this->hasSpecialChar($seoAttr)) {
					foreach ($product->getAttributes() as $attr) {
						if ($checkedSeoAttr = $this->registerSeoFilter($attr->getAttributeCode())) {



							$fitting['id'][$checkedSeoAttr] = $attr->getSource()->getOptionId($attr->getAttributeCode());
							$fitting['value'][$checkedSeoAttr] = $product->getResource()->getAttribute($attr->getAttributeCode())->getFrontend()->getValue($product);


							/*
							$attrFront = $filter->getFilter()->getAttributeModel()->getFrontend();
							$attr = $attrFront->getAttribute();

							$attrFront = $filter->getFilter()->getAttributeModel()->getFrontend();
							$attr = $attrFront->getAttribute();
							$seoAttr = $this->registerSeoFilter($attr->getAttributeCode());
							$id = $attr->getSource()->getOptionId($filter->getValue());
							$value = $attrFront->getOption($filter->getValue());
							*/
						}
					}
				} elseif (($checkedSeoAttr = $this->registerSeoFilter($seoAttr)) && method_exists($this, $method = 'get' . uc_words($checkedSeoAttr, '', '_'))) {
					$fitting[$checkedSeoAttr] = $this->{$method}();
				} else {
					if ($checkedSeoAttr = $this->registerSeoFilter($seoAttr)) {
						//$fitting[$seoAttr] = $this->prepareAttributeValue($seoAttr);
						$this->prepareAttributeValue($seoAttr, $fitting);
					}
				}
			}
		}
		//Zend_Debug::dump($fitting); die(__METHOD__);
		return $fitting;
	}

	private function getCategory() {
		$product = Mage::registry('current_product');
		$categories = $product->getCategoryCollection()
			->addAttributeToSelect('name');

		$names = array();
		foreach($categories as $category) {
			$names[] = $category->getName();
		}

		return implode(', ', $names);
	}

	private function getAttributeSetName() {
		$product = Mage::registry('current_product');
		$attributeSetName = Mage::getModel('eav/entity_attribute_set')->load($product->getAttributeSetId())->getAttributeSetName();

		return $attributeSetName;
	}

	/**
	 * @param $attrCode
	 * @return string
	 * @link http://stackoverflow.com/a/17292572
	 */
	private function prepareAttributeValue($attrCode, & $fitting) {
		/** @var Mage_Catalog_Model_Resource_Eav_Attribute */
		$product = Mage::registry('current_product');
		$attr = $product->getResource()->getAttribute($attrCode);

		if (in_array($attr->getFrontendInput(), array('select', 'multiselect'))) {
			$fitting['id'][$attrCode] = $attr->getSource()->getOptionId($attrCode);
			$fitting['value'][$attrCode] = $attr->getFrontend()->getValue($product);
		} else {
			$method = 'get' . uc_words($attrCode, '', '_');
			$fitting['id'][$attrCode] = null;
			$fitting['value'][$attrCode] = $product->{$method}();
		}
	}

}

<?php
/**
 * Meta tags overwrite
 *
 * @category Agere
 * @package Popov_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 17.04.14 18:27
 */

abstract class Popov_Seo_Model_MetaTag_Abstract implements Popov_Seo_Model_MetaTag_MetaInterface {

	/**
	 * Rule collection
	 *
	 * @var Popov_Seo_Model_Resource_Rule_Collection
	 */
	protected $rules;

	/**
	 * Filtered attributes for SEP
	 *
	 * @var array
	 * 		key - original attribute name (may consist pattern)
	 * 		value - processed attribute name (without pattern)
	 */
	protected $filteredAttrs = array();

	/**
	 * Best rule for current request
	 *
	 * @var Popov_Seo_Model_Rule
	 */
	protected $fittingRule;

	/**
	 * Tags content
	 *
	 * @var array
	 */
	protected $metaTags = array(
		'title' => '',
		'description' => '',
		'keywords' => '',
		//... if need can be other meta tags
	);

	public function __construct(Popov_Seo_Model_Resource_Rule_Collection $rules) {
		$this->rules = $rules;
	}

	/**
	 * Main method which to do all preparation and set change in meta tags
	 *
	 * @return bool
	 */
	public function run() {
		$this->preRun();

		$this->changeHeadMetaTags();

		$this->postRun();
	}

	protected function preRun() {}

	protected function postRun() {}

	/**
	 * Get meta tag type
	 *
	 * @return string
	 * @todo return object Type (category, product)
	 */
	public function getType() {
		return $this->rules->getFirstItem()->getType();
	}

	public function handleSeoAttributes($attrs) {
		if (!is_array($attrs)) {
			/** @var Agere_Base_Helper_String $stringHelper */
			$stringHelper = Mage::helper('agere_base/string');
			$attrs = $stringHelper->create(str_replace(' ', '', $attrs))->explode(',');
		}
		sort($attrs);

		return $attrs;
	}

	/**
	 * Get value of tag
	 *
	 * @param string $name Tag name (title) or meta property (description, keywords...)
	 * @return string
	 */
	public function getValueOf($name) {
		if (isset($this->metaTags[$name])) {
			return $this->metaTags[$name];
		}

		return false;
 	}

	/**
	 * Add rule item
	 *
	 * @param Popov_Seo_Model_Rule $rule
	 * @return $this
	 */
	public function addRule(Popov_Seo_Model_Rule $rule) {
		$this->rules->addItem($rule);
	}

	/**
	 * Get rules collection
	 *
	 * @return Popov_Seo_Model_Resource_Rule_Collection
	 */
	public function getRules() {
		return $this->rules;
	}

	public function prepareMetaTags() {
		$bestRule = $this->getFittingRule();
		$fittingAttrs = $this->getFittingFilterAttributes()['value'];

		if (!$bestRule) {
			return false;
		}

		$fittingAttrs = $this->prepareAttrs($fittingAttrs);

		foreach ($this->getMetaTags() as $tag => $value) {
			$tagValue = $this->prepareValue($bestRule->getData($tag), $fittingAttrs);
			$this->metaTags[$tag] = $tagValue;
		}

		return true;
	}

	protected function prepareAttrs($attrs) {
		foreach ($attrs as $name => $attr) {
			if (is_array($attr)) {
				$attrs[$name] = implode(', ', $this->handleSeoAttributes($attr));
			}
		}

		return $attrs;
	}

	protected function prepareValue($value, $vars) {
		/** @var Agere_Base_Helper_String $stringHelper */
		$stringHelper = Mage::helper('agere_base/string');
		
		//Zend_Debug::dump($value);
		//Zend_Debug::dump($vars);
		$vars = array_merge($this->getAdditionalValues(), $vars);
		$tagValue = $stringHelper->create($value)->sprintfKey($vars);
		$tagValue = $this->interpret($tagValue);

		return $tagValue;
	}

	protected function interpret($string) {
		$processed = $string;
		$prepared = array();
		preg_match_all('/{(.*?)}:([a-zA-Z|]+)/', $string, $matches);

		// there is function for prepare string
		if (isset($matches[2])) {
			foreach ($matches[2] as $key => $handlersStr) {
				$handlers = explode('|', $handlersStr);
				$prepared[$key] = $matches[1][$key];
				foreach ($handlers as $handler) {
					$prepared[$key] = $this->prepare($handler, $prepared[$key]);
				}
			}
			$processed = str_replace($matches[0], $prepared, $string);
		}

		return $processed;
	}

	/**
	 * Prepare value
	 *
	 * @param $handler
	 * @param $string
	 * @return string
	 */
	protected function prepare($handler, $string) {
		$prepared = '';
		if (method_exists($this, $method = 'prepare' . ucfirst($handler))) {
			$prepared = $this->{$method}($string);
		} elseif (function_exists($handler)) {
			$prepared = call_user_func($handler, $string);
		} else {
			Mage::throwException('Cannot found string handler with name ' . $handler);
		}

		return $prepared;
	}

	protected function prepareStrtolower($string, $encoding = 'utf-8') {
		return mb_strtolower($string, $encoding);
	}

	protected function prepareUcfirst($string, $encoding = 'utf-8') {
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);
		return mb_strtoupper($firstChar, $encoding) . $then;
	}

	protected function prepareTranslate($string) {
		return Mage::helper('popov_seo')->__($string);
	}

	protected function prepareTranslit($string) {
		static $trans = false;
		if (!$trans) { // little cache
			$trans = Mage::getConfig()->getNode("global/dictionaries/popov_seo/transliterations");
		}

		if (isset($trans->{$string}) && ($value = (string) $trans->{$string})) {
			$string = $value;
		}

		return $string;
	}

	/**
	 * Left for compatibility
	 *
	 * @param $attrCode
	 * @return string
	 */
	protected function registerSeoFilter($attrCode) {
		//$subSeoAttr = rtrim(preg_replace("/[^A-Za-z0-9_]/", '', $attrCode), '_');
		$this->filteredAttrs[$attrCode] = $attrCode;

		return $attrCode;
	}

	/*protected function checkSeoFilter($attrCode) {
		$seoAttrs = $this->getSeoAttributes();

		foreach ($seoAttrs as $seoAttr) {
			$pattern = '/^' . $seoAttr . '/';
			if (preg_match($pattern, $attrCode)) {
				$subSeoAttr = rtrim(preg_replace("/[^A-Za-z0-9_]/", '', $seoAttr), '_');
				$this->filteredAttrs[$seoAttr] = $subSeoAttr;

				return $subSeoAttr;
			}
		}

		return false;
	}*/

	protected function getFittingRule() {
		if (!$this->fittingRule) {
			$fittingAttrs = $this->getFittingFilterAttributes()['id'];

			$best = array();
			$default = false;
			$rules = $this->getRules();
			$numFittingAttrs = count($fittingAttrs);

			//Zend_Debug::dump($rules->count());
			//Zend_Debug::dump($rules->getSelect()->assemble()); die(__METHOD__);

			foreach ($rules as $key => $rule) {
				if (!($attrs = $rule->getSeoAttributeFilters())) {
					$attrs = $rule->getSeoAttributes();
					$default = $rule;
				}

				$parts = explode(';', $attrs);

				// support single value per filter if you need more in one filter then you have to explode and sort $id
				// and sort Popov_Seo_Model_MetaTag_Category::getFittingFilterAttributes() ~207 line
				$best[$key] = 0;
				foreach ($parts as $condition) {
					$condParts = explode(':', $condition);
					$attr = $condParts[0];
					$id = isset($condParts[1]) ? $this->handleSeoAttributes($condParts[1]) : null;
					$fittingId = $this->handleSeoAttributes($fittingAttrs[$attr]);

					//Zend_Debug::dump($condition); //die(__METHOD__);
					//Zend_Debug::dump($id); //die(__METHOD__);
					//Zend_Debug::dump($fittingId); //die(__METHOD__);

					if ($id && $fittingId == $id) {
						$best[$key]++;
					} elseif (!$id) {
						$best[$key]++;
					}
				}

				if (($default->getId() != $rule->getId()) && $numFittingAttrs === $best[$key]) {
					$this->fittingRule = $rule;
					break;
				}
			}

			if (!$this->fittingRule && $default) {
				$this->fittingRule = $default;
			}

			//ksort($best);
			//Zend_Debug::dump($best);
			//Zend_Debug::dump(get_class($this->fittingRule)); die(__METHOD__.__LINE__);

			//$this->fittingRule = $rules->getItems()[end($best)];
		}

		return $this->fittingRule;
	}

	/*protected function prepareSqlFilters() {
		//$fittingAttrKeys = array_keys($this->getFittingFilterAttributes()['id']);
		$fittingAttrs = $this->getFittingFilterAttributes()['id'];

		$filters = array();
		foreach ($fittingAttrs as $code => $value) {
			$filters['simple'][$code] = $code;
			$filters['advanced'][$code] = $code . ':' . $value;
		}

		Zend_Debug::dump($filters); die(__METHOD__);


		$rules = $this->getRules();
	}*/

	protected function getAdditionalValues() {
		$vars = array();
		$vars['websiteCode'] = Mage::app()->getWebsite()->getCode();
		$vars['storeCode'] = Mage::app()->getStore()->getCode();
		$vars['website'] = Mage::app()->getWebsite()->getName();
		$vars['store'] = Mage::app()->getStore()->getName();

		return $vars;
	}

	protected function hasSpecialChar($string) {
		if(!preg_match('/^[a-zA-Z0-9_]+$/', $string)) {
			return true;
		}
		return false;
	}

	public function getMetaTags() {
		return $this->metaTags;
	}

	protected function changeHeadMetaTags() {
		$prepared = $this->prepareMetaTags();

		/** @var Mage_Page_Block_Html_Head $head */
		$head = Mage::app()->getLayout()->getBlock('head');

		if ($head) {
			$metaTags = $this->getMetaTags();
			foreach($metaTags as $tag => $value) {
				if (!$prepared || !$this->canOverwriteDefaultMetaTags()) {
					$value = $head->getData($tag) . ' ' .  $value;
				}
				/*if (trim($value) && ($end = $this->getGeneralEndingOfMetaTags())) {
					$value = sprintf('%s - %s', $value,  $end);
				}*/
				$head->setData($tag, $value);
			}
			Mage::dispatchEvent('a_meta_tags_change_after', array('block' => $head));
		}
	}

	/**
	 * @todo Create admin GUI and add this option
	 */
	protected function canOverwriteDefaultMetaTags() {
		return true;
	}

	/**
	 * @return bool
	 * @deprecated
	 */
	/*protected function getGeneralEndingOfMetaTags() {
		return false;
	}*/

}
<?php
/**
 * Meta tags overwrite
 *
 * @category Popov
 * @package Popov_Seo
 * @author Popov Sergiy <popov@popov.com.ua>
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
	protected $fittingRules;

	/**
	 * Tags content
	 *
	 * @var array
	 */
	protected $tags = array(
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

		$this->changeTags();

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
			/** @var Popov_Base_Helper_String $stringHelper */
			$stringHelper = Mage::helper('popov_base/string');
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

	protected function prepareAttrs($attrs) {
		foreach ($attrs as $name => $attr) {
			if (is_array($attr)) {
				$attrs[$name] = implode(', ', $this->handleSeoAttributes($attr));
			}
		}

		return $attrs;
	}

	protected function prepareValue($value, $vars) {
		/** @var Popov_Base_Helper_String $stringHelper */
		$stringHelper = Mage::helper('popov_base/string');

		//Zend_Debug::dump($value);
		//Zend_Debug::dump($vars);
		$vars = array_merge($this->getAdditionalValues(), $vars);
		$tagValue = $stringHelper->create($value)->sprintfKey($vars);
		$tagValue = $this->interpret($tagValue);

		return $tagValue;
	}

	protected function stripValue($value)
    {
        return preg_replace(array('/{(.*?)}:([a-zA-Z|]+)/', '/%[a-zA-Z_]+%/'), '', $value);
        //return preg_replace_callback('/{(.*?)}:([a-zA-Z|]+)/', function() { return ''; }, $value);
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

	protected function getFittingRules() {
		if (is_null($this->fittingRules)) {
			$fittingAttrs = $this->getFittingFilterAttributes()['id'];

			$best = array();
			$default = false;
			$rules = $this->getRules();
			$numFittingAttrs = count($fittingAttrs);

			foreach ($rules as $key => $rule) {
				if (!($attrs = $rule->getSeoOptionFilters())) {
					$attrs = $rule->getSeoAttributes();
					$default[$rule->getContext()] = $rule;
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

					if ($id && $fittingId === $id) {
						$best[$key]++;
					} elseif (!$id) {
						$best[$key]++;
					}
				}

				// If set of rules is only with seo_option_filters without default rule
                // then we shouldn't check it, otherwise we check if default and current rule is not equal
				if ((!isset($default[$rule->getContext()]) || ($default[$rule->getContext()]->getId() != $rule->getId()))
                    && $numFittingAttrs === $best[$key]
                ) {
					$this->fittingRules[$rule->getContext()] = $rule;
					//break;
				}
			}

			if (!$this->fittingRules && $default) {
				$this->fittingRules = $default;
			}

			//ksort($best);
			//Zend_Debug::dump($best);
			//Zend_Debug::dump(get_class($this->fittingRule)); die(__METHOD__.__LINE__);

			//$this->fittingRule = $rules->getItems()[end($best)];
		}

		return $this->fittingRules;
	}

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

	/*public function getMetaTags() {
		return $this->metaTags;
	}*/

    public function prepareTags()
    {
        $bestRules = $this->getFittingRules();
        $fittingAttrs = $this->getFittingFilterAttributes()['value'];

        if (!$bestRules) {
            return false;
        }

        $fittingAttrs = $this->prepareAttrs($fittingAttrs);

        $this->tags = array();
        foreach ($bestRules as /*$tag => */$rule) {
            $tagValue = $this->prepareValue($rule->getContent(), $fittingAttrs);
            $tagValue = $this->stripValue($tagValue);
            $this->tags[$rule->getContext()] = $tagValue;
        }

        return $this->tags;
    }

	protected function changeTags()
    {
        $metaTags = $this->prepareTags();
        foreach($metaTags as $tag => $value) {
            if (Mage::getStoreConfig('popov_section/settings/allow_change_' . $tag)) {
                $method = (strpos($tag, 'meta') !== false)
                    ? 'meta'
                    : $tag;

                $method = 'change' . ucfirst(ucwords($method));
                #if (!method_exists($this, $method)) {
                #    Mage::throwException(sprintf('There is no registered handler for "%s" tag', $tag));
                #}
                $this->{$method}($value, $tag);
            }

        }
	}

	protected function changeMeta($value, $tag = null)
    {
        /** @var Mage_Page_Block_Html_Head $head */
        if ($head = Mage::app()->getLayout()->getBlock('head')) {
            $name = substr($tag, 4); // skip "meta_"
            $head->setData($name, $value);

            Mage::dispatchEvent('p_meta_tags_change_after', array('block' => $head));
        }
    }

	protected function changeH1($value, $tag = null)
    {}

	protected function changeDescription($value, $tag = null)
    {}
}
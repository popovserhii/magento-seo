<?php
/**
 * Meta tag interface
 *
 * @category Agere
 * @package Popov_Seo
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 21.04.14 15:27
 */

interface Popov_Seo_Model_MetaTag_MetaInterface {

	/**
	 * Main method which to do all preparation and set change in meta tags
	 * This method is wrapper for prepare() and other auxiliary method
	 *
	 * @return bool
	 */
	public function run();

	/**
	 * Get meta tag type
	 *
	 * @return string
	 * @todo return object Type (Category, Product)
	 */
	public function getType();

	/**
	 * Get value of tag
	 *
	 * @param string $name Tag name (title) or meta property (description, keywords...)
	 * @return string
	 */
	public function getValueOf($name);


	/**
	 * Add rule item
	 *
	 * @param Popov_Seo_Model_Rule $rule
	 * @return $this
	 */
	public function addRule(Popov_Seo_Model_Rule $rule);

	/**
	 * Get rules collection
	 *
	 * @return Popov_Seo_Model_Resource_Rule_Collection
	 */
	public function getRules();

	/**
	 * Get fitting filter attributes
	 *
	 * @return array
	 */
	public function getFittingFilterAttributes();

}
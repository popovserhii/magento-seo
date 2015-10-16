<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 29.10.14 18:46
 */

class Agere_Seo_SeoBlockController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$post = $this->getRequest()->getPost();

		if ($post && isset($post['href']))
		{
			/** @var Agere_Seo_Helper_Data $seoHelper */
			$seoHelper = Mage::helper('agere_seo');
			$params = $seoHelper->parseUrl($post['href']);

			$result['seoText'] = '';

			if ($params)
			{
				Mage::register('params', $params);

				/** @var Agere_Seo_Block_SeoBlock $seoBlock */
				$seoBlock = Mage::getBlockSingleton('agere_seo/seoBlock');
				$seoBlock->setTemplate('agere/seo/block/top.phtml');
				$result['seoText'] = $seoBlock->renderView();
			}

			/** @var Mage_Core_Helper_Data $core */
			$core = Mage::helper('core');
			$this->getResponse()->setBody($core->jsonEncode($result));
		}
		else
		{
			$this->_redirect('/');
		}
	}

}
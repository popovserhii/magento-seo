<?php
/**
 * Enter description here...
 *
 * @category Agere
 * @package Agere_<package>
 * @author Popov Sergiy <popov@agere.com.ua>
 * @datetime: 16.10.14 12:41
 */

class Popov_Seo_Adminhtml_SeoBlockController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout()->_setActiveMenu('popov_seo');
		$this->_title($this->__('Seo Block'));
		$this->_addContent($this->getLayout()->createBlock('popov_seo/adminhtml_seoBlock'));
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function editAction()
	{
		$id = (int) $this->getRequest()->getParam('id');
		Mage::register('current_seoblock', Mage::getModel('popov_seo/seoblock')->load($id));

		$this->loadLayout()->_setActiveMenu('popov_seo');
		$this->_addContent($this->getLayout()->createBlock('popov_seo/adminhtml_seoBlock_edit'));
		$this->renderLayout();
	}

	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost())
		{
			try {
				$model = Mage::getModel('popov_seo/seoblock');
				$model->setData($data)->setId($this->getRequest()->getParam('id'));
				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Seo Block was saved successfully'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array(
					'id' => $this->getRequest()->getParam('id')
				));
			}

			return;
		}

		Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}

	public function deleteAction()
	{
		if ($id = $this->getRequest()->getParam('id'))
		{
			try {
				Mage::getModel('popov_seo/seoblock')->setId($id)->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Seo Block was deleted successfully'));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $id));
			}
		}

		$this->_redirect('*/*/');
	}

}
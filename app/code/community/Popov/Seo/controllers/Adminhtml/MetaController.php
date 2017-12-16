<?php
/**
 * @category Popov
 * @package Popov_Seo
 * @author Serhii Popov <popow.serhii@gmail.com>
 * @datetime: 16.10.14 12:41
 */
class Popov_Seo_Adminhtml_MetaController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		//die(__METHOD__);
		//$this->_title($this->__('Meta Tags'));
		// see layout
		//$this->_addContent($this->getLayout()->createBlock('popov_robots/adminhtml_robots'));

		$this->_initAction();
		$this->renderLayout();
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function editAction() {
		//$this->_initAction();

		$id = (int) $this->getRequest()->getParam('id');
		$model = Mage::getModel('popov_seo/rule')->load($id);

		//$this->loadLayout()->_setActiveMenu('popov_seo');

		$data = Mage::getSingleton('adminhtml/session')->getMetaData(true);
		if (!empty($data)) {
			$model->setData($data);
		}

		Mage::register('current_popov_meta', $model);

		$this->_initAction()
			->_addBreadcrumb($id ? $this->__('Edit Rule') : $this->__('New Rule'), $id ? $this->__('Edit Rule') : $this->__('New Rule'))
			//->_addContent($this->getLayout()->createBlock('popov_robots/adminhtml_robots_edit')->setData('action', $this->getUrl('*/*/save')))
			->renderLayout();
	}

	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			try {
				//$data = $this->_filterDates($data, array('updated_at'));

                if (isset($data['stores'])) {
                    if (in_array('0', $data['stores'])) {
                        $data['store_id'] = '0';
                    } else {
                        $data['store_id'] = implode(',', $data['stores']);
                    }
                    unset($data['stores']);
                }

				/** @var Popov_Seo_Model_MetaTag_Factory $factory */
				$factory = Mage::getModel('popov_seo/metaTag_factory');

				$data['seo_attributes'] = implode(';', $factory->create($data['type'])->handleSeoAttributes($data['seo_attributes']));
				$data['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
				if (!$this->getRequest()->getParam('id')) {
					$data['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
				}

				$model = Mage::getModel('popov_seo/rule');
				$model->setData($data)->setId($this->getRequest()->getParam('id'));
				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('SEO Rule was saved successfully'));
				Mage::getSingleton('adminhtml/session')->setMetaData(false);
				$this->_redirect('*/*/');
			} catch (Mage_Core_Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this SEO Rule'));
			}

			return;
		}

		Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}

	public function copyAction()
    {
        $id = (int) $this->getRequest()->getParam('id');
        try {
            $model = Mage::getModel('popov_seo/rule')->load($id);

            $data = $model->getData();
            $data['id'] = null;
            $data['is_active'] = 0;
            $data['updated_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');
            $data['created_at'] = Mage::getModel('core/date')->date('Y-m-d H:i:s');

            $copy = Mage::getModel('popov_seo/rule');
            $copy->setData($data);
            $copy->save();
            $id = $copy->getId();

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('SEO Rule was copied successfully'));
            Mage::getSingleton('adminhtml/session')->setMetaData(false);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/edit', array('id' => $id));
    }

	public function deleteAction() {
		if ($id = $this->getRequest()->getParam('id')) {
			try {
				Mage::getModel('popov_seo/meta')->setId($id)->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('SEO Rule was deleted successfully'));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $id));
			}
		}
		$this->_redirect('*/*/');
	}

	/**
	 * Initialize action
	 *
	 * Here, we set the breadcrumbs and the active menu
	 *
	 * @return Mage_Adminhtml_Controller_Action
	 */
	protected function _initAction() {
		$this->loadLayout()
			// Make the active menu match the menu config nodes (without 'children' inbetween)
			->_setActiveMenu('popov_seo/popov_seo_meta')
			->_title($this->__('SEO'))->_title($this->__('Meta Tags'))
			->_addBreadcrumb($this->__('SEO'), $this->__('SEO'))
			->_addBreadcrumb($this->__('Meta Tags'), $this->__('Meta Tags'));

		return $this;
	}

	/**
	 * Check currently called action by permissions for current user
	 *
	 * @return bool
	 */
	protected function _isAllowed()	{
		return Mage::getSingleton('admin/session')->isAllowed('popov_seo/popov_seo_meta');
	}

}
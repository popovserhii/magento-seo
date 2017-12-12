<?php
/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_<package>
 * @author Popov Sergiy <popov@popov.com.ua>
 * @datetime: 16.10.14 12:56
 */

class Popov_Seo_Block_Adminhtml_SeoBlock_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected function _prepareCollection()
	{
		/** @var Popov_Seo_Model_Resource_Seoblock_Collection $collection */
		$collection = Mage::getModel('popov_seo/seoblock')->getCollection();
		$collection->getSelect()->joinLeft(
			array('category' => $collection->getTable('catalog/category')),
			'main_table.category_id = category.entity_id',
			array('category.parent_id', 'category.path')
		);

		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$helper = Mage::helper('popov_seo');

		$this->addColumn('entity_id', array(
			'header'	=> $helper->__('ID'),
			'index'		=> 'entity_id'
		));

		$this->addColumn('category_id', array(
			'header'    => $helper->__('Category'),
			'index'     => 'category_id',
			'type'      => 'options',
			'options'	=> Mage::getModel('Popov_Seo_Model_System_Config_Category')->toOptionArray(),
			'renderer'	=> 'Popov_Seo_Block_Widget_Grid_Column_Renderer_Category',
		));

		$this->addColumn('url', array(
			'header'	=> $helper->__('Url'),
			'index'		=> 'url',
			'type'		=> 'text',
		));

		$this->addColumn('store_id', array(
			'header'    => $helper->__('Store View'),
			'index'     => 'store_id',
			'type'      => 'store',
		));

		$this->addColumn('is_active', array(
			'header'    => $helper->__('Status'),
			'index'     => 'is_active',
			'type'      => 'options',
			'options'   => array(
				1 => $helper->__('Enabled'),
				0 => $helper->__('Disabled')
			),
		));

		$this->addColumn('action',
			array(
				'header'    =>  $helper->__('Action'),
				'width'     => '60',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption'   => $helper->__('Edit'),
						'url'       => array('base'=> '*/*/edit'),
						'field'     => 'id'
					),
				),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
			));

		return parent::_prepareColumns();
	}

	public function getRowUrl($model)
	{
		return $this->getUrl('*/*/edit', array(
			'id' => $model->getId(),
		));
	}

}
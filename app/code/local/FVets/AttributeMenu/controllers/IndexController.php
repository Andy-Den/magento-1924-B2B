<?php

class FVets_AttributeMenu_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->loadLayout();

		$crumbs = Mage::app()->getLayout()->getBlock('breadcrumbs');
		$crumbs->addCrumb('home', array(
			'label' => 'home',
			'title' => 'Ir para a Home',
			'link' => Mage::getUrl('')
		));

		$params = Mage::app()->getRequest()->getParam('type');
		if ($params) {
			$crumbs->addCrumb($params, array(
				'label' => $params,
				'title' => $params
			));
		}

		if ($params)
		{
			$this->getLayout()->getBlock('head')->setTitle(ucfirst($params));
		}

		$this->renderLayout();

		//para a requisiçao ajax "ver mais produtos";
		Mage::dispatchEvent('controller_action_postdispatch_catalog_category_view', array('controller_action' => $this));
	}
}

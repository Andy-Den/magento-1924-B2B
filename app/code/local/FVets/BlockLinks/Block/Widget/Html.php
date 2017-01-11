<?php

class FVets_BlockLinks_Block_Widget_Html extends Mage_Page_Block_Html
{

	protected $_widget;
	protected $_items = null;

	public function _beforeToHtml()
	{

		if (!$this->_items)
			$this->_widget = $this->createWidget($this->getWidgetId());

		parent::_beforeToHtml();
	}

	public function _toHtml()
	{
		if($this->_widget) {
			return $this->_widget->toHtml();
		}

		if ($this->_items)
		{
			$html = '';
			foreach ($this->_items as $item)
			{
				$html .= $item->toHtml();
			}
			return $html;
		}

		return null;
	}

	public function addItem($widgetId) {
		if (!$this->_items) {
			$this->_items = array();
		}

		$this->_items[] = $this->createWidget($widgetId);
	}

	private function createWidget($widgetId)
	{
		$widget = Mage::getModel('widget/widget_instance')->load($widgetId);

		$widget = $this
			->getLayout()
			->createBlock($widget->getType(),
				$widget->getTitle(),
				$widget->getWidgetParameters());

		return $widget;
	}


 }
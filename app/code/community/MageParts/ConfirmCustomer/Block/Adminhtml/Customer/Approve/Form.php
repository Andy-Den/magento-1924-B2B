<?php

class MageParts_ConfirmCustomer_Block_Adminhtml_Customer_Approve_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {

		$form = new Varien_Data_Form(array(
			'id'        => 'edit_form',
			'action'    => $this->getData('action'),
			'method'    => 'post',
			'enctype'   => 'multipart/form-data'
		));

		$customer = Mage::registry('current_customer');

		$fieldset = $form->addFieldset('confirmcustomer', array(
			'legend' => Mage::helper('confirmcustomer')->__('General Information'),
			'class' => 'fieldset-wide',
		));

		$eventElem = $fieldset->addField('mp_cc_is_approved', 'select', array(
			'name' => 'mp_cc_is_approved',
			'label' => Mage::helper('confirmcustomer')->__('Approved'),
			'required' => true,
			'class' => 'validate-approval-status',
			//'values' => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray()
			'values' => Mage::getSingleton('confirmcustomer/source_options')->getAllOptions(),
			'onchange' => 'saveMpCcIsNotApproved(this)'
		));

		$eventElem->setAfterElementHtml($this->getMpCssIsApprovedJs());

		$fieldset->addField('fvets_salesrep', 'multiselect', array(
			'name' => 'fvets_salesrep',
			'label' => Mage::helper('fvets_salesrep')->__('Sales Rep'),
			'required' => true,
			'values' => Mage::getSingleton('fvets_salesrep/source_salesrep')->getAllOptions()
		));

		$fieldset->addField('id_erp', 'text', array(
			'name' => 'id_erp',
			'label' => Mage::helper('confirmcustomer')->__('ID ERP'),
			'required' => true
		));

		$fieldset->addField('group_id', 'select', array(
			'name' => 'group_id',
			'label' => Mage::helper('core')->__('Table Price'),
			'required' => true,
			'class' => 'validate-group-table-price',
			'values' => Mage::getSingleton('customer/customer_attribute_source_group')->getAllOptions()
		));

		//habilitando / desabilitando seleção de storeview de acordo com configuração
		$websiteId = $customer->getWebsiteId();
		$website = Mage::getModel('core/website')->load($websiteId);
		$multiStoreWebsites = Mage::getStoreConfig('confirmcustomer/general/store_select');
		if ($multiStoreWebsites && in_array($website->getCode(), explode(',', $multiStoreWebsites))) {
			$fieldset->addField('store_view', 'multiselect', array(
				'name' => 'store_view',
				'label' => Mage::helper('core')->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('fvets_customer/eav_entity_attribute_source_storeview')->getAllOptions()
			));
		}

		$fieldset->addField('hive_of_activity', 'multiselect', array(
			'name' => 'hive_of_activity',
			'label' => Mage::helper('core')->__('Hive of Activity'),
			'required' => true,
			'values' => Mage::getSingleton('fvets_customer/eav_entity_attribute_source_hiveactivity')->getAllOptions()
		));

		$fieldset->addField('restriction_group', 'multiselect', array(
			'name' => 'restriction_group',
			'label' => Mage::helper('core')->__('Restriction Group'),
			'required' => false,
			'values' => Mage::getSingleton('fvets_catalogrestrictiongroup/source_restrictiongroup')->getAllOptions()
		));

		$fieldset->addField('confirm_conditions', 'multiselect', array(
			'name' => 'confirm_conditions',
			'label' => Mage::helper('core')->__('Payment Conditions'),
			'required' => false,
			'values' => Mage::getSingleton('fvets_payment/condition_source_customer')->getAllOptions()
			//fvets_salesrep/source_salesrep
		));

		if ($customer->getId()) {
			$form->addField('entity_id', 'hidden', array(
				'name' => 'customer_id',
			));

			$customerAlreadyApproved = $customer->getData('mp_cc_is_approved') == MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED ? true : false;
			$collection = implode(',', Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup')->getCollection()->addCustomerFilter($customer)->getAllIds());
			$customer->setData('restriction_group', $collection);
			
			$conditionsCollection = Mage::getModel('fvets_payment/condition')->getCollection()->addAdminCustomerFilter($customer->getId())->getAllIds();
			$customer->setData('confirm_conditions', $conditionsCollection);

			$customerData = $customer->getData();
			if(!$customerAlreadyApproved) {
				$restrictionGroups = Mage::getSingleton('fvets_catalogrestrictiongroup/source_restrictiongroup')->getAllOptions();
				$restrictionGroupsArray = array();
				foreach($restrictionGroups as $restrictionGroup) {
					$restrictionGroupsArray[] = $restrictionGroup['value'];
				}
				$customerData['restriction_group'] = implode(',', $restrictionGroupsArray);

				$conditions = Mage::getSingleton('fvets_payment/condition_customer')->getAllOptions();
				$conditionsArray = array();
				foreach($conditions as $condition) {
					$conditionsArray[] = $condition['value'];
				}
				$customerData['confirm_conditions'] = implode(',', $conditionsArray);
			}
			$form->setValues($customerData);
		}

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
    }

	function getMpCssIsApprovedJs()
	{
		return '
		<script type="text/javascript">
			var classElements = [];
			function saveMpCcIsNotApproved(el)
			{
				if (el.value != \'1\')
				{
					if (classElements.length == 0)
					{
						var form  = document.getElementById(\'confirmcustomer\');
						var elems = form.getElementsByTagName("*");


						for (var index in elems) {
							var formEl = elems[index];
							if (formEl.name != undefined && formEl.name != \'\')
							{
								if (hasClass(formEl, \'required-entry\'))
								{
									classElements[formEl.name] = [formEl, formEl.className];
									formEl.className = formEl.className.replace(/\brequired-entry\b/,\'\')
								}

								if (hasClass(formEl, \'validate-group-table-price\'))
								{
									classElements[formEl.name] = [formEl, formEl.className];
									formEl.className = formEl.className.replace(/\bvalidate-group-table-price\b/,\'\')
								}
							}
						};
					}
				}
				else
				{
					for (var index in classElements) {
						if (classElements[index][0] != undefined)
						{
							var formEl = classElements[index][0];
							formEl.className = classElements[index][1];
						}
					};
					classElements = [];
				}
			}

			function hasClass(element, cls) {
				return (\' \' + element.className + \' \').indexOf(\' \' + cls + \' \') > -1;
			}

			function domReady () {
				saveMpCcIsNotApproved(document.getElementById(\'mp_cc_is_approved\'));
			}

			// Mozilla, Opera, Webkit
			if ( document.addEventListener ) {
				document.addEventListener( "DOMContentLoaded", function(){
					document.removeEventListener( "DOMContentLoaded", arguments.callee, false);
					domReady();
				}, false );

		// If IE event model is used
			} else if ( document.attachEvent ) {
				// ensure firing before onload
				document.attachEvent("onreadystatechange", function(){
					if ( document.readyState === "complete" ) {
						document.detachEvent( "onreadystatechange", arguments.callee );
						domReady();
					}
				});
			}
		</script>
	';
	}

}
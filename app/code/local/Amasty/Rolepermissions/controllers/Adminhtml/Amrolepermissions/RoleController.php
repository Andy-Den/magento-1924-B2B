<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Adminhtml_Amrolepermissions_RoleController extends Mage_Adminhtml_Controller_Action
{
    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('id');
        $sourceRole = Mage::getModel('admin/role')->load($id);

        if ($sourceRole->getId())
        {
            $newName = $sourceRole->getRoleName() . $this->__(' (duplicated)');
            $role = Mage::getModel('admin/role')
                ->setData($sourceRole->getData())
                ->setData('role_name', $newName)
                ->unsetData('role_id')
                ->save();

            foreach (array('admin/rules', 'amrolepermissions/rule') as $model)
            {
                $rules = Mage::getResourceModel($model.'_collection');
                $rules->addFieldToFilter('role_id', $sourceRole->getId());

                foreach ($rules as $rule)
                {
                    $rule
                        ->unsetData('rule_id')
                        ->setData('role_id', $role->getId())
                        ->save();
                }
            }

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Role Successfully Duplicated'));

            $url = $this->getUrl('adminhtml/permissions_role/editrole', array('rid' => $role->getId()));

            $this->getResponse()
                ->setRedirect($url)
                ->sendResponse();
        }
    }
}

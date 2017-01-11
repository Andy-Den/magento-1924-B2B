<?php

require_once 'Mage/Adminhtml/controllers/System/Convert/ProfileController.php';

class FVets_ImportPromotions_Adminhtml_System_Convert_ProfileController extends Mage_Adminhtml_System_Convert_ProfileController
{
	/**
	 * Save profile action
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost())
		{
			if (!$this->_initProfile('profile_id'))
			{
				return;
			}
			$profile = Mage::registry('current_convert_profile');

			// Prepare profile saving data
			if (isset($data))
			{
				$profile->addData($data);
			}

			try
			{
				$profile->save();

				$loadVars = Mage::helper('fvets_importpromotions')->getActionXmlVars('load', $profile);
				if ($loadVars)
				{
					$this->uploadFile($loadVars);
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__('The profile has been saved.'));
			} catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setConvertProfileData($data);
				$this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('id' => $profile->getId())));
				return;
			}
			if ($this->getRequest()->getParam('continue'))
			{
				$this->_redirect('*/*/edit', array('id' => $profile->getId()));
			} else
			{
				$this->_redirect('*/*');
			}
		} else
		{
			Mage::getSingleton('adminhtml/session')->addError(
				$this->__('Invalid POST data (please check post_max_size and upload_max_filesize settings in your php.ini file).')
			);
			$this->_redirect('*/*');
		}
	}

	public function uploadFile($loadVars)
	{

		try
		{
			if (isset($_FILES['generalfile']['name']) && !empty($_FILES['generalfile']['name']))
			{
				try
				{
					if (!isset($loadVars['filename']))
					{
						Mage::throwException('O atributo "filename" deve ser definido no perfil');
					}

					$uploader = Mage::getModel('core/file_uploader', 'generalfile');
					$uploader->setAllowedExtensions(array('csv'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);

					if (isset($loadVars['path']))
					{
						$path = Mage::getBaseDir() . DS . $loadVars['path'] . DS;
					} else
					{
						$path = Mage::getBaseDir('var') . DS . 'import' . DS;
					}

					$filename = $loadVars['filename'];
					$uploader->save($path, $filename);

					$logFileName = date('Y-m-d H:i:s');
					$logFileName = str_replace('-', '', $logFileName);
					$logFileName = str_replace(':', '', $logFileName);
					$logFileName = str_replace(' ', '-', $logFileName);
					copy($path . $filename, $path . $logFileName . '-' . $filename);

					Mage::getSingleton('adminhtml/session')->addSuccess(
						$this->__('O arquivo foi enviado.')
					);

				} catch (Exception $e)
				{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fvets_importpromotions')->__('O arquivo nao foi enviado.'));
				}
			}
		} catch (Exception $e)
		{
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
	}
}

<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class FVets_Core_Model_Layout_Update extends FVets_MultipleHandles_Model_Core_Layout_Update
{
    /**
     * Collect and merge layout updates from file
     *
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param integer|null $storeId
     * @return Mage_Core_Model_Layout_Element
     */
    public function getFileLayoutUpdatesXml($area, $package, $theme, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        /* @var $design Mage_Core_Model_Design_Package */
        $design = Mage::getSingleton('core/design_package');
        $layoutXml = null;
        $elementClass = $this->getElementClass();
        $updatesRoot = Mage::app()->getConfig()->getNode($area.'/layout/updates');
        Mage::dispatchEvent('core_layout_update_updates_get_after', array('updates' => $updatesRoot));
        $updateFiles = array();
        foreach ($updatesRoot->children() as $updateNode) {
            if ($updateNode->file) {
                $module = $updateNode->getAttribute('module');
                if ($module && Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $module, $storeId)) {
                    continue;
                }
                $updateFiles[] = (string)$updateNode->file;
            }
        }
        // custom local layout updates file - load always last
				$updateFiles[] = 'skin.xml';
			  $updateFiles[] = 'local.xml';
        $layoutStr = '';
        foreach ($updateFiles as $file) {
            $filename = $design->getLayoutFilename($file, array(
                '_area'    => $area,
                '_package' => $package,
                '_theme'   => $theme
            ));
            if (!is_readable($filename)) {
                continue;
            }

			if ($file == 'local.xml')
			{
				$xml = new SimpleXMLElement($filename, 0, true);
				$dom = dom_import_simplexml($xml);
				$dom->ownerDocument->xinclude();

				//$dom->appendChild($dom->getElementsByTagName('root')->item(0));

				$xml = simplexml_import_dom($dom);
				$fileStr = $xml->asXML();
			}
			else
			{
				$fileStr = file_get_contents($filename);
			}

            $fileStr = str_replace($this->_subst['from'], $this->_subst['to'], $fileStr);

            $fileXml = simplexml_load_string($fileStr, $elementClass);

            if (!$fileXml instanceof SimpleXMLElement) {
                continue;
            }

			if ($file == 'local.xml')
			{
				$count = 0;
				while($node = $fileXml->descend('include_layout')[$count])
				{
					//Incluir os valores do xml exatamente no local em que eles deveriam estar, mas sem a tag <include_layout>
					$fileStr = str_replace($node->asXml(), $node->innerXml(), $fileStr);
					$count++;
				}

				$fileXml = simplexml_load_string($fileStr, $elementClass);
			}

            $layoutStr .= $fileXml->innerXml();
        }
        $layoutXml = simplexml_load_string('<layouts>'.$layoutStr.'</layouts>', $elementClass);
        return $layoutXml;
    }
}

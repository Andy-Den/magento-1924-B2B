<?php
/**
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Magemaven
 * @package     Magemaven_Lesscss
 * @copyright   Copyright (c) 2012 Sergey Storchay <r8@r8.com.ua>
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
require_once(Mage::getBaseDir('lib') . DS . 'lessphp' . DS .'lessc.inc.php');

class Magemaven_Lesscss_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Get file extension in lower case
     *
     * @param $file
     * @return string
     */
    public function getFileExtension($file)
    {
        return strtolower(pathinfo($file, PATHINFO_EXTENSION));
    }

    /**
     * Compile less file and return full path to created css
     *
     * @param $file
     * @return string
     */
    public function compile($file)
    {
        if (!$file) {
            return '';
        }

        try {

            Varien_Profiler::start('skin' . explode('skin', $file)[1]);
//            $targetFilename = Mage::getBaseDir('media')
//                . DS . 'lesscss' . DS . md5($file . Mage::getDesign()->getSkinBaseDir()) . '.css';

					$targetFilename = Mage::getBaseDir('media')
						. DS . 'lesscss' . DS . str_replace(array('/', '.'), array('_', ''), explode('/frontend/', Mage::getDesign()->getSkinBaseDir())[1] . explode('less', $file)[1]) . '.css';

            $cacheKey = md5('less_' . Mage::getDesign()->getSkinBaseDir() . $file);

            //#biscoito
            $handles = Mage::app()->getLayout()->getUpdate()->getHandles();
            if (in_array('v2_less_variables_importer', $handles)) {
                $importFile = Mage::getDesign()->getFilename('less/variables.less', array('_type' => 'skin', '_ignoreless' => true));
            } else {
                $importFile = Mage::getDesign()->getFilename('less/default.less', array('_type' => 'skin', '_ignoreless' => true));
            }

            /** @var $cacheModel Mage_Core_Model_Cache */
            $cacheModel = $cache = Mage::getSingleton('core/cache');
            $cache = $cacheModel->load($cacheKey);
            if ($cache) {
                $cache = @unserialize($cache);
            }

            if (!file_exists($targetFilename)) {
                $cache = false;
            }

            $lastUpdated = (isset($cache['updated'])) ? $cache['updated'] : 0;

			$recompile = false;
			if (!isset($cache['import_mtime'])  || (is_array($cache) && $cache['import_mtime'] != filemtime($importFile)))
			{
				$recompile = true;
			}

            $cache = lessc::cexecute(($cache) ? $cache : $file, $recompile, $importFile);

			$cache['import_mtime'] = filemtime($importFile);

            if ($cache['updated'] > $lastUpdated) {
                if (!file_exists(dirname($targetFilename))) {
                    mkdir(dirname($targetFilename), 0777, true);
                }

                file_put_contents($targetFilename, $cache['compiled']);
                $cacheModel->save(serialize($cache), $cacheKey);
            }
            Varien_Profiler::stop('skin' . explode('skin', $file)[1]);

        } catch (Exception $e) {
            Mage::logException($e);
			var_dump($e);
            $targetFilename = '';
        }

        return $targetFilename;
    }
}

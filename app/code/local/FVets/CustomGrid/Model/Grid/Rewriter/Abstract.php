<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FVets
 * @package    FVets_CustomGrid
 * @copyright  Copyright (c) 2014 Benoît Leulliette <carlos.farah@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class FVets_CustomGrid_Model_Grid_Rewriter_Abstract
    extends Varien_Object
{
    const REWRITE_CODE_VERSION = 1; // bump this value when significant changes are made to the rewrite code
    
    protected function _getBlcgClassPrefix()
    {
        return 'FVets_CustomGrid_Block_Rewrite_';
    }
    
    protected function _getBlcgClass($originalClass, $gridType)
    {
        $classParts = array_map('ucfirst', array_map('strtolower', explode('_', $originalClass)));
        return $this->_getBlcgClassPrefix() . implode('_', $classParts);
    }
    
    abstract protected function _rewriteGrid($fvetscgClass, $originalClass, $gridType);
    
    final public function rewriteGrid($originalClass, $gridType)
    {
        $fvetscgClass = $this->_getBlcgClass($originalClass, $gridType);
        $rewriteSuccess = false;
        
        try {
            if (!class_exists($originalClass, true)) {
                Mage::throwException(Mage::helper('customgrid')->__('The original class "%s" does not exist', $originalClass));
            }
            if (class_exists($fvetscgClass, false)) {
                Mage::throwException(Mage::helper('customgrid')->__('The rewriting class "%s" already exists', $fvetscgClass));
            }
            
            $this->_rewriteGrid($fvetscgClass, $originalClass, $gridType);
            
            if (!class_exists($fvetscgClass, true)) {
                Mage::throwException(Mage::helper('customgrid')->__('The generated rewriting class "%s" can not be found', $fvetscgClass));
            }
            
            $rewriteSuccess = true;
            
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('customgrid')->__('An error occured while rewriting "%s" : "%s" (rewriter: "%s")', $gridType, $e->getMessage(), $this->getId()));
        }
        
        return ($rewriteSuccess ? $fvetscgClass : false);
    }
    
    protected function _getRewriteCode($fvetscgClass, $originalClass, $gridType)
    {
        return 'class '.$fvetscgClass.' extends '.$originalClass.'
{
    private $_fvetscg_gridModel   = null;
    private $_fvetscg_typeModel   = null;
    private $_fvetscg_filterParam = null;
    private $_fvetscg_exportInfos = null;
    private $_fvetscg_exportedCollection    = null;
    private $_fvetscg_holdPrepareCollection = false;
    private $_fvetscg_prepareEventsEnabled  = true;
    private $_fvetscg_defaultParameters     = array();
    private $_fvetscg_collectionCallbacks   = array(
        \'before_prepare\'     => array(),
        \'after_prepare\'      => array(),
        \'before_set_filters\' => array(),
        \'after_set_filters\'  => array(),
        \'before_set\'         => array(),
        \'after_set\'          => array(),
        \'before_export_load\' => array(),
        \'after_export_load\'  => array(),
    );
    private $_fvetscg_additionalAttributes = array();
    private $_fvetscg_mustSelectAdditionalAttributes   = false;
    
    public function getModuleName()
    {
        $module = $this->getData(\'module_name\');
        
        if (is_null($module)) {
            if (!$class = get_parent_class($this)) {
                $class = get_class($this);
            }
            $module = substr($class, 0, strpos($class, \'_Block\'));
            $this->setData(\'module_name\', $module);
        }
        
        return $module;
    }
    
    public function setCollection($collection)
    {
        if (!is_null($this->_fvetscg_typeModel)) {
            $this->_fvetscg_typeModel->beforeGridSetCollection($this, $collection);
        }
        $this->_fvetscg_launchCollectionCallbacks(\'before_set\', array($this, $collection));
        $return = parent::setCollection($collection);
        $this->_fvetscg_launchCollectionCallbacks(\'after_set\', array($this, $collection));
        if (!is_null($this->_fvetscg_typeModel)) {
            $this->_fvetscg_typeModel->afterGridSetCollection($this, $collection);
        }
        return $return;
    }
    
    public function getCollection()
    {
        $collection = parent::getCollection();
        if ($this->_fvetscg_mustSelectAdditionalAttributes
            && ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract)
            && count($this->_fvetscg_additionalAttributes)) {
            $this->_fvetscg_mustSelectAdditionalAttributes = false;
            foreach ($this->_fvetscg_additionalAttributes as $attr) {
                $collection->joinAttribute($attr[\'alias\'], $attr[\'attribute\'], $attr[\'bind\'], $attr[\'filter\'], $attr[\'join_type\'], $attr[\'store_id\']);
            }
        }
        return $collection;
    }
    
    protected function _setFilterValues($data)
    {
        if ($this->_fvetscg_holdPrepareCollection) {
            return $this;
        } else {
            if (!is_null($this->_fvetscg_gridModel)) {
                $data = $this->_fvetscg_gridModel->verifyGridBlockFilters($this, $data);
            }
            $this->_fvetscg_launchCollectionCallbacks(\'before_set_filters\', array($this, $this->_collection, $data));
            $return = parent::_setFilterValues($data);
            $this->_fvetscg_launchCollectionCallbacks(\'after_set_filters\', array($this, $this->_collection, $data));
            return $return;
        }
    }
    
    protected function _prepareCollection()
    {
        // @todo should we use getCollection() for callbacks, but temporary passing the "_fvetscg_mustSelectAdditionalAttributes" flag to false ?
        if (!is_null($this->_fvetscg_typeModel)) {
            $this->_fvetscg_typeModel->beforeGridPrepareCollection($this, $this->_fvetscg_prepareEventsEnabled);
        }
        if ($this->_fvetscg_prepareEventsEnabled) {
            Mage::getSingleton(\'customgrid/observer\')->beforeGridPrepareCollection($this);
            $this->_fvetscg_launchCollectionCallbacks(\'before_prepare\', array($this, $this->_collection, true));
            $return = parent::_prepareCollection();
            $this->_fvetscg_launchCollectionCallbacks(\'after_prepare\', array($this, $this->_collection, true));
            Mage::getSingleton(\'customgrid/observer\')->afterGridPrepareCollection($this);
        } else {
            $this->_fvetscg_launchCollectionCallbacks(\'before_prepare\', array($this, $this->_collection, false));
            $return = parent::_prepareCollection();
            $this->_fvetscg_launchCollectionCallbacks(\'after_prepare\', array($this, $this->_collection, false));
        }
        if (!is_null($this->_fvetscg_typeModel)) {
            $this->_fvetscg_typeModel->afterGridPrepareCollection($this, $this->_fvetscg_prepareEventsEnabled);
        }
        return $return;
    }
    
    public function _exportIterateCollection($callback, array $args)
    {
        if (!is_array($this->_fvetscg_exportInfos)) {
            return parent::_exportIterateCollection($callback, $args);
        } else {
            if (!is_null($this->_fvetscg_exportedCollection)) {
                $originalCollection = $this->_fvetscg_exportedCollection;
            } else {
                $originalCollection = $this->getCollection();
            }
            if ($originalCollection->isLoaded()) {
                Mage::throwException(Mage::helper(\'customgrid\')->__(\'This grid does not seem to be compatible with the custom export. If you wish to report this problem, please indicate this class name : "%s"\', get_class($this)));
            }
            
            $exportPageSize = (isset($this->_exportPageSize) ? $this->_exportPageSize : 1000);
            $infos = $this->_fvetscg_exportInfos;
            $total = (isset($infos[\'custom_size\']) ?
                intval($infos[\'custom_size\']) :
                (isset($infos[\'size\']) ? intval($infos[\'size\']) : $exportPageSize));
                
            if ($total <= 0) {
                return;
            }
            
            $fromResult = (isset($infos[\'from_result\']) ? intval($infos[\'from_result\']) : 1);
            $pageSize   = min($total, $exportPageSize);
            $page       = ceil($fromResult/$pageSize);
            $pitchSize  = ($fromResult > 1 ? $fromResult-1 - ($page-1)*$pageSize : 0);
            $break      = false;
            $count      = null;
            
            while ($break !== true) {
                $collection = clone $originalCollection;
                $collection->setPageSize($pageSize);
                $collection->setCurPage($page);
                
                if (!is_null($this->_fvetscg_typeModel)) {
                    $this->_fvetscg_typeModel->beforeGridExportLoadCollection($this, $collection);
                }
                $this->_fvetscg_launchCollectionCallbacks(\'before_export_load\', array($this, $collection, $page, $pageSize));
                $collection->load();
                $this->_fvetscg_launchCollectionCallbacks(\'after_export_load\', array($this, $collection, $page, $pageSize));
                if (!is_null($this->_fvetscg_typeModel)) {
                    $this->_fvetscg_typeModel->afterGridExportLoadCollection($this, $collection);
                }
                
                if (is_null($count)) {
                    $count = $collection->getSize();
                    $total = min(max(0, $count-$fromResult+1), $total);
                    if ($total == 0) {
                        $break = true;
                        continue;
                    }
                    $first = true;
                    $exported = 0;
                }
                
                $page++;
                $i = 0;
                
                foreach ($collection as $item) {
                    if ($first) {
                        if ($i++ < $pitchSize) {
                            continue;
                        } else {
                            $first = false;
                        }
                    }
                    if (++$exported > $total) {
                        $break = true;
                        break;
                    }
                    call_user_func_array(array($this, $callback), array_merge(array($item), $args));
                }
            }
        }
    }
    
    public function fvetscg_isExport()
    {
        return $this->_isExport;
    }
    
    public function setDefaultPage($page)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $page = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'page\', $page, null, false, $this->_defaultPage);
        }
        return parent::setDefaultPage($page);
    }
    
    public function setDefaultLimit($limit)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $limit = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'limit\', $limit, null, false, $this->_defaultLimit);
        }
        return parent::setDefaultLimit($limit);
    }
    
    public function setDefaultSort($sort)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $sort = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'sort\', $sort, null, false, $this->_defaultSort);
        }
        return parent::setDefaultSort($sort);
    }
    
    public function setDefaultDir($dir)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $dir = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'dir\', $dir, null, false, $this->_defaultDir);
        }
        return parent::setDefaultDir($dir);
    }
    
    public function setDefaultFilter($filter)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $filter = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'filter\', $filter, null, false, $this->_defaultFilter);
        }
        return parent::setDefaultFilter($filter);
    }
    
    public function fvetscg_setDefaultPage($page)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $page = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'page\', $this->_defaultPage, $page, true);
        }
        return parent::setDefaultPage($page);
    }
    
    public function fvetscg_setDefaultLimit($limit, $forced=false)
    {
        if (!$forced && !is_null($this->_fvetscg_gridModel)) {
            $limit = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'limit\', $this->_defaultLimit, $limit, true);
        }
        return parent::setDefaultLimit($limit);
    }
    
    public function fvetscg_setDefaultSort($sort)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $sort = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'sort\', $this->_defaultSort, $sort, true);
        }
        return parent::setDefaultSort($sort);
    }
    
    public function fvetscg_setDefaultDir($dir)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $dir = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'dir\', $this->_defaultDir, $dir, true);
        }
        return parent::setDefaultDir($dir);
    }
    
    public function fvetscg_setDefaultFilter($filter)
    {
        if (!is_null($this->_fvetscg_gridModel)) {
            $filter = $this->_fvetscg_gridModel->getGridBlockDefaultParamValue(\'filter\', $this->_defaultFilter, $filter, true);
        }
        return parent::setDefaultFilter($filter);
    }
    
    public function fvetscg_setGridModel($model)
    {
        $this->_fvetscg_gridModel = $model;
        return $this;
    }
    
    public function fvetscg_getGridModel()
    {
        return $this->_fvetscg_gridModel;
    }
    
    public function fvetscg_setTypeModel($model)
    {
        $this->_fvetscg_typeModel = $model;
        return $this;
    }
    
    public function fvetscg_setFilterParam($param)
    {
        $this->_fvetscg_filterParam = $param;
        return $this;
    }
    
    public function fvetscg_getFilterParam()
    {
        return $this->_fvetscg_filterParam;
    }
    
    public function fvetscg_setExportInfos($infos)
    {
        $this->_fvetscg_exportInfos = $infos;
    }
    
    public function fvetscg_getStore()
    {
        if (method_exists($this, \'_getStore\')) {
            return $this->_getStore();
        }
        $storeId = (int)$this->getRequest()->getParam(Mage::helper(\'customgrid/config\')->getStoreParameter(\'store\'), 0);
        return Mage::app()->getStore($storeId);
    }
    
    public function fvetscg_getSaveParametersInSession()
    {
        return $this->_saveParametersInSession;
    }
    
    public function fvetscg_getSessionParamKey($name)
    {
        return $this->getId().$name;
    }
    
    public function fvetscg_getPage()
    {
        if ($this->getCollection() && $this->getCollection()->isLoaded()) {
            return $this->getCollection()->getCurPage();
        }
        return $this->getParam($this->getVarNamePage(), $this->_defaultPage);
    }
    
    public function fvetscg_getLimit()
    {
        return $this->getParam($this->getVarNameLimit(), $this->_defaultLimit);
    }
    
    public function fvetscg_getSort($checkExists=true)
    {
        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        if (!$checkExists || (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex())) {
            return $columnId;
        }
        return null;
    }
    
    public function fvetscg_getDir()
    {
        if ($this->fvetscg_getSort()) {
            return (strtolower($this->getParam($this->getVarNameDir(), $this->_defaultDir)) == \'desc\') ? \'desc\' : \'asc\';
        }
        return null;
    }
    
    public function fvetscg_getCollectionSize()
    {
        if ($this->getCollection()) {
            return $this->getCollection()->getSize();
        }
        return null;
    }
    
    public function fvetscg_addAdditionalAttribute(array $attribute)
    {
        $this->_fvetscg_additionalAttributes[] = $attribute;
        return $this;
    }
    
    public function fvetscg_setExportedCollection($collection)
    {
        $this->_fvetscg_exportedCollection = $collection;
        return $this;
    }
    
    public function fvetscg_holdPrepareCollection()
    {
        $this->_fvetscg_holdPrepareCollection = true;
        return $this;
    }
    
    public function fvetscg_finishPrepareCollection()
    {
        if ($this->getCollection()) {
            $this->_fvetscg_holdPrepareCollection = false;
            $this->_fvetscg_prepareEventsEnabled  = false;
            $this->_fvetscg_mustSelectAdditionalAttributes = true;
            $this->_prepareCollection();
        }
        return $this;
    }
    
    public function fvetscg_removeColumn($id)
    {
        if (array_key_exists($id, $this->_columns)) {
            unset($this->_columns[$id]);
            if ($this->_lastColumnId == $id) {
                $keys = array_keys($this->_columns);
                $this->_lastColumnId = array_pop($keys);
            }
        }
        return $this;
    }
    
    public function fvetscg_resetColumnsOrder()
    {
        $this->_columnsOrder = array();
        return $this;
    }
    
    public function fvetscg_addCollectionCallback($type, $callback, $params=array(), $addNative=true)
    {
        $this->_fvetscg_collectionCallbacks[$type][] = array(
            \'callback\'   => $callback,
            \'params\'     => $params,
            \'add_native\' => $addNative,
        );
        end($this->_fvetscg_collectionCallbacks[$type]);
        $key = key($this->_fvetscg_collectionCallbacks);
        reset($this->_fvetscg_collectionCallbacks);
        return $key;
    }
    
    public function fvetscg_removeCollectionCallback($type, $id)
    {
        if (isset($this->_fvetscg_collectionCallbacks[$type][$id])) {
            unset($this->_fvetscg_collectionCallbacks[$type][$id]);
        }
        return $this;
    }
    
    protected function _fvetscg_launchCollectionCallbacks($type, $params=array())
    {
        foreach ($this->_fvetscg_collectionCallbacks[$type] as $callback) {
            call_user_func_array(
                $callback[\'callback\'],
                array_merge(
                    array_values($callback[\'params\']),
                    ($callback[\'add_native\']? array_values($params) : array())
                )
            );
        }
        return $this;
    }
}';
    }
}
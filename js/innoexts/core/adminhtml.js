/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 *
 * @category    Innoexts
 * @package     Innoexts_Core
 * @copyright   Copyright (c) 2013 Innoexts (http://www.innoexts.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var EditableGridFormControl = Class.create(varienForm, {

    initialize : function($super, grid, formId, elementIdPrefix, elementNames, defaults, validationUrl) {
        $super(formId, validationUrl);
        this.grid = grid;
        this.elementIdPrefix = elementIdPrefix;
        this.elementNames = elementNames;
        this.defaults = defaults;
        this.grid.rowClickCallback = this.rowClick.bind(this);
        this.grid.initRowCallback = this.rowInit.bind(this);
        this.grid.rows.each( function(row) { this.rowInit(this.grid, row); }.bind(this));
    }, 
    rowClick : function(grid, event) { return; }, 
    rowInit : function(grid, row) {
        var select = $(row).down('.action-select');
        if (select) {
            select.writeAttribute('onchange', null);
            select.observe('change', this.actionSelectChange.bind(this));
        }
    }, 
    actionSelectChange: function(event) {
        var select = Event.element(event);
        if (!select.value || !select.value.isJSON()) {
            return;
        }
        var config = select.value.evalJSON();
        if (config.confirm && !window.confirm(config.confirm)) {
            return;
        }     
        if (config.href) {
            if (config.name == 'edit') {
                this.doEdit(config.href);
            } else if (config.name == 'delete') {
                this.doDelete(config.href);
            }    
        }    
        select.options[0].selected = true;
        return;
    }, 
    doDelete : function(url) {
        if (url) {
            new Ajax.Request(url, {
                area : $(this.grid.containerId), 
                onComplete : this.doDeleteComplete.bind(this)
            });
        }
    }, 
    doDeleteComplete : function(transport) {
        var result = transport.responseText.evalJSON();
        if (result.messages) {
            $('messages').update(result.messages);
        }
        if (!result.error) {
            this.grid.reload();
        }
    }, 
    getElementNames : function() {
        return this.elementNames;
    }, 
    getDefaults : function() {
        return this.defaults;
    }, 
    getDefault : function(name) {
        var defaults = this.getDefaults();
        if (defaults[name]) {
            return defaults[name];
        } else {
            return null;
        }
    }, 
    getElementIdPrefix : function() {
        return this.elementIdPrefix;
    }, 
    getElement : function(name) {
        return $(this.getElementIdPrefix() + name);
    }, 
    hasElement : function(name) {
        return (this.getElement(name)) ? true : false;
    }, 
    setValue : function(name, value) {
        if (this.hasElement(name)) {
            this.getElement(name).setValue(value);
        }
    }, 
    setValues : function(values) {
        var self = this;
        this.getElementNames().each(function(elementName) {
            self.setValue(elementName, (
                (values[elementName] && values[elementName] !== null) ? values[elementName] : self.getDefault(elementName)
            ));
        });
    }, 
    doAdd : function() {
        this.setValues(this.getDefaults());
    }, 
    doEdit : function(url) {
        if (url) {
            new Ajax.Request(url, {
                area : $(this.grid.containerId), 
                onComplete : this.doEditComplete.bind(this)
            });
        }
    }, 
    doEditComplete : function(transport) {
        var result = transport.responseText.evalJSON();
        if (result.messages) $('messages').update(result.messages);
        if (result.data) this.setValues(result.data);
    }, 
    _submit : function() {
        if (this.submitUrl) {
            var params = Form.serializeElements($(this.formId).select('input', 'select', 'textarea'), true);
            params.form_key = FORM_KEY;
            $('messages').update();
            new Ajax.Request(this.submitUrl, {
                parameters : params, 
                method : 'post', 
                area : $(this.formId), 
                onComplete : this.doSubmitComplete.bind(this)
            });
        }
    }, 
    doSubmitComplete : function(transport) {
        var result = transport.responseText.evalJSON();
        if (result.messages) $('messages').update(result.messages);
        if (!result.error) this.grid.reload();
    }
});

/**
 * Form Element Control
 */
var FormElementControl = new Class.create();

FormElementControl.prototype = {

    initialize : function(data) {
        if (!data) data = {};
        if (data.elementId) {
            this.elementId          = data.elementId;
        }
        this.render();
    }, 
    
    getElement : function() {
        return $(this.elementId);
    }, 
    
    getActionsElement : function() {
        return $(this.getElement().select('.control-actions').first());
    }, 
    
    getBodyElement : function() {
        return $(this.getElement().select('.control-body').first());
    }, 
    
    render : function() {
        
    }
};

/**
 * Checkbox Control
 */
var CheckboxControl = Class.create(FormElementControl, {

    initialize : function($super, data) {
        if (!data) data = {};
        if (data.dependent) {
            this.dependent = data.dependent;
        }
        $super(data);
        var self = this;
        this.getInputElement().observe('click', function () { self.render(); });
    }, 
    
    getInputElement : function() {
        return $(this.getBodyElement().select('.checkbox').first());
    }, 
    
    render : function() {
        if (this.dependent) {
            var inputEl = this.getInputElement();
            this.dependent.each(function (el) {
                if (!inputEl.checked) {
                    $(el).enable();
                } else {
                    $(el).disable();
                }
            });
        }
    }
});
    

/**
 * Multiple Checkbox Control
 */
var MultipleCheckboxControl = Class.create(FormElementControl, {

    initialize : function($super, data) {
        $super(data);
        var self = this;
        this.getSelectAllElement().observe('click', function () { self.selectAll(); });
        this.getUnselectAllElement().observe('click', function () { self.unselectAll(); });
    }, 
    
    getSelectAllElement : function() {
        return $(this.getActionsElement().select('.select-all').first());
    }, 
    
    getUnselectAllElement : function() {
        return $(this.getActionsElement().select('.unselect-all').first());
    }, 
    
    selectAll : function() {
        this.getBodyElement().select('.checkbox').each(function(el) {
            el.checked = true;
        });
    }, 
    
    unselectAll : function() {
        this.getBodyElement().select('.checkbox').each(function(el) {
            el.checked = false;
        });
    }
});

/**
 * Tree Control
 */
var TreeControl = Class.create(FormElementControl, {

    initialize : function($super, data) {
        if (!data) data = {};
        if (data.tree) {
            this.tree = data.tree;
        }
        $super(data);
        var self = this;
        this.getCollapseAllElement().observe('click', function () { self.collapseAll(); });
        this.getExpandAllElement().observe('click', function () { self.expandAll(); });
        this.getSelectAllElement().observe('click', function () { self.selectAll(); });
        this.getUnselectAllElement().observe('click', function () { self.unselectAll(); });
    }, 
    
    getCollapseAllElement : function() {
        return $(this.getActionsElement().select('.collapse-all').first());
    }, 
    
    getExpandAllElement : function() {
        return $(this.getActionsElement().select('.expand-all').first());
    }, 
    
    getSelectAllElement : function() {
        return $(this.getActionsElement().select('.select-all').first());
    }, 
    
    getUnselectAllElement : function() {
        return $(this.getActionsElement().select('.unselect-all').first());
    }, 
    
    collapseAll : function() {
        this.tree.collapseAll();
    }, 
    
    expandAll : function() {
        this.tree.expandAll();
    }, 
    
    onSelectAll : function(treeNode) {
        var inputEl = treeNode.getUI().getEl().select('input').first();
        if (!inputEl.checked) {
            $(inputEl).click();
        }
    }, 
    
    onUnselectAll : function(treeNode) {
        var inputEl = treeNode.getUI().getEl().select('input').first();
        if (inputEl.checked) {
            $(inputEl).click();
        }
    }, 
    
    selectAll : function() {
        this.tree.getRootNode().cascade(this.onSelectAll, this);
    }, 
    
    unselectAll : function() {
        this.tree.getRootNode().cascade(this.onUnselectAll, this);
    }
});
    
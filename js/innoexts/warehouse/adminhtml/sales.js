/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2013 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */
var WarehouseAdminOrder = Class.create(AdminOrder, {
    
    initialize : function($super, data) {
        this.stockId = false;
        $super(data);
    }, 
    itemsReset : function(){
        var area = ['sidebar', 'items', 'shipping_method', 'billing_method','totals', 'giftmessage'];
        var fieldsPrepare = {reset_items: 1};
        fieldsPrepare = Object.extend(fieldsPrepare, this.productConfigureAddFields);
        this.productConfigureSubmit('quote_items', area, fieldsPrepare);
        this.orderItemChanged = false;
    }, 
    setStockShippingMethod : function(stockId, method) {
        var data = {};
        data['order[shipping_method][' + stockId +']'] = method;
        this.loadArea(['shipping_method', 'totals', 'billing_method'], true, data);
    }, 
    setStockId : function(id) {
        this.stockId = id;
        this.loadArea(['data'], true);
    }, 
    prepareParams : function($super, params) {
        params = $super(params);
        if (!params) {
            params = {};
        }
        if (!params.stock_id) {
            params.stock_id = this.stockId;
        }
        return params;
    }
});


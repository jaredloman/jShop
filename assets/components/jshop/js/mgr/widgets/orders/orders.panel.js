jShop.panel.Orders = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('js.orders_management')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,items: [{
                title: _('js.orders')
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('js.orders_desc')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'jshop-grid-orders'
                    ,cls: 'main-wrapper'
                    ,preventRender: true
            	}]
            }]
        }]
    });
    jShop.panel.Orders.superclass.constructor.call(this,config);
};
Ext.extend(jShop.panel.Orders,MODx.Panel);
Ext.reg('jshop-panel-orders',jShop.panel.Orders);
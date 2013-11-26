Ext.onReady(function() {
    MODx.load({ xtype: 'jshop-page-orders'});
});
 
jShop.page.Orders = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'jshop-panel-orders'
            ,renderTo: 'jshop-panel-orders-div'
        }]
    });
    jShop.page.Orders.superclass.constructor.call(this,config);
};
Ext.extend(jShop.page.Orders,MODx.Component);
Ext.reg('jshop-page-orders',jShop.page.Orders);
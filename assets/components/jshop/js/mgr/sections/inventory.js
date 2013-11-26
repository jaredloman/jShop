Ext.onReady(function() {
    MODx.load({ xtype: 'jshop-page-home'});
});
 
jShop.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'jshop-panel-home'
            ,renderTo: 'jshop-panel-home-div'
        }]
    });
    jShop.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(jShop.page.Home,MODx.Component);
Ext.reg('jshop-page-home',jShop.page.Home);
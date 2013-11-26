var jShop = function(config) {
    config = config || {};
    jShop.superclass.constructor.call(this,config);
};
Ext.extend(jShop,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},formpanel:{},combo:{},config: {}
});
Ext.reg('jshop',jShop);
jShop = new jShop();
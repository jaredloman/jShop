jShop.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('js.management')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
			,activeTab: parseInt(MODx.request.tab) || 0
            ,items: [{
                title: _('js.items')
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('js.management_desc')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'jshop-grid-items'
                    ,cls: 'main-wrapper'
                    ,preventRender: true
            }]
            },{
                title: _('js.categories')
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('js.category_management_desc')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'jshop-grid-categories'
                    ,cls: 'main-wrapper'
                    ,preventRender: true
                }]
            }]
        }]
    });
    jShop.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(jShop.panel.Home,MODx.Panel);
Ext.reg('jshop-panel-home',jShop.panel.Home);
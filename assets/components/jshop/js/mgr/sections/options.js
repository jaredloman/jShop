Ext.onReady(function() {
    var o = MODx.load({ xtype: 'jshop-page-options'});
	o.show();
});

jShop.page.Options = function(config) {
    config = config || {};
    Ext.applyIf(config,{
		renderTo: 'jshop-panel-options-div'
		,buttons: [{
            process: 'cancel',
            text: _('js.back'),
            handler: function () {
                window.location.href = '?a='+MODx.request['a']+'&action=item&id='+MODx.request['pid'];
            }
        }]
        ,components: [{
			xtype: 'jshop-panel-header'
		},{
			xtype: 'modx-panel'
			,id: 'jshop-panel-tabs'
			,width: '98%'
			,bodyStyle: 'padding: 10px 10px 10px 10px;'
			,border: true
			,defaults: {
				border: false
				,autoHeight: true
				,bodyStyle: 'padding: 5px 8px 5px 5px;'
			}
			,items: [{
				html: (jShop.record) ? '<h2>'+jShop.record.name+':</h2>' : _('js.option_nf')
				,border: false
				,id: 'jshop-item-page-header'
				,cls: 'modx-page-header'
			},{ 
				xtype: 'jshop-grid-values'
				,url: jShop.config.connectorUrl
				,baseParams: {
					action: 'mgr/options/values/getlist'
					,optId: MODx.request['id']
				} 
				,border: false
			}]
		}]
    });
    jShop.page.Options.superclass.constructor.call(this,config);
};
Ext.extend(jShop.page.Options,MODx.Component);
Ext.reg('jshop-page-options',jShop.page.Options);

jShop.panel.Header = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'container modx-formpanel'
        ,items: [{
            html: '<h2>'+_('js.option_management')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        }]
    });
    jShop.panel.Header.superclass.constructor.call(this,config);
};
Ext.extend(jShop.panel.Header,MODx.Panel);
Ext.reg('jshop-panel-header',jShop.panel.Header);
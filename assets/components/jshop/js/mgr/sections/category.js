Ext.onReady(function() {
    var o = MODx.load({ xtype: 'jshop-page-category'});
	if ((jShop.record) && (Ext.getCmp('jshop-panel-category'))) {
		Ext.getCmp('jshop-panel-category').getForm().setValues(jShop.record);
	}
	o.show();
});
 
jShop.page.Category = function(config) {
    config = config || {};
    Ext.applyIf(config,{
		renderTo: 'jshop-panel-category-div'
		,buttons: [{
            process: 'submit',
            text: _('save'),
            handler: function () {
                var panel = Ext.getCmp('jshop-panel-category');   
				if (panel.getForm().isValid()) {
                    Ext.getCmp('jshop-panel-category').submit();
					window.location.href = '?a='+MODx.request['a']+'&action=inventory&tab=1'
                } else {
                    MODx.msg.alert(_('error'),_('correct_errors'))
                } 
            }
        },'-',{
            process: 'cancel',
            text: _('js.back'),
            handler: function () {
                window.location.href = '?a='+MODx.request['a']+'&action=inventory&tab=1';
            }
        }]
        ,components: [{
			xtype: 'jshop-panel-header'
		},{
			xtype: 'modx-tabs'
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
				title: _('js.category')
				,items: [{
					html: (jShop.record) ? '<h2>'+jShop.record.name+'</h2>' : '<h2>'+_('js.category_create')+'</h2>'
					,border: false
					,id: 'jshop-page-category-header'
					,cls: 'modx-page-header'
				},{ 
					xtype: 'jshop-panel-category' 
					,border: false
				}]
			}/*,{
				title: _('js.subcategories')
				,disabled: (jShop.record) ? false : true
				,items: [{ 
					xtype: 'jshop-grid-subcategories'
					,url: jShop.config.connectorUrl
					,baseParams: {
						action: 'mgr/category/getsublist'
						,parent: (jShop.record) ? jShop.record['id'] : 0
					}
					,border: false 
				}]
			}*/]
		}]
    });
    jShop.page.Category.superclass.constructor.call(this,config);
};
Ext.extend(jShop.page.Category,MODx.Component);
Ext.reg('jshop-page-category',jShop.page.Category);

jShop.panel.Header = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'container modx-formpanel'
        ,items: [{
            html: '<h2>'+_('js.category_management')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        }]
    });
    jShop.panel.Header.superclass.constructor.call(this,config);
};
Ext.extend(jShop.panel.Header,MODx.Panel);
Ext.reg('jshop-panel-header',jShop.panel.Header);

Ext.onReady(function() {
    var o = MODx.load({ xtype: 'jshop-page-item'});
	if ((jShop.record) && (Ext.getCmp('jshop-panel-item'))) {
		Ext.getCmp('jshop-panel-item').getForm().setValues(jShop.record);
	}
	o.show();
});
 
jShop.page.Item = function(config) {
    config = config || {};
    Ext.applyIf(config,{
		renderTo: 'jshop-panel-item-div'
		,buttons: [{
            process: 'submit'
			,id: 'js-btn-save'
            ,text: _('save')
            ,handler: function () {
                var panel = Ext.getCmp('jshop-panel-item');   
				if (panel.getForm().isValid()) {
                    Ext.getCmp('jshop-panel-item').submit();
                } else {
                    MODx.msg.alert(_('error'),_('correct_errors'))
                } 
            }
        },'-',{
            process: 'cancel',
            text: _('js.back'),
            handler: function () {
                window.location.href = '?a='+MODx.request['a']+'&action=inventory';
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
				title: _('js.item')
				,items: [{
					html: (jShop.record) ? '<h2>'+jShop.record.title+'</h2>' : '<h2>'+_('js.item_new')+'</h2><p>'+_('js.item_new_desc')+'</p>'
					,border: false
					,id: 'jshop-item-page-header'
					,cls: 'modx-page-header'
				},{ 
					xtype: 'jshop-panel-item' 
					,border: false
				}]
			},{
				title: _('js.options')
				,disabled: (jShop.record) ? false : true
				,items: [{ 
					xtype: 'jshop-grid-options'
					,url: jShop.config.connectorUrl
					,baseParams: {
						action: 'mgr/options/getlist'
						,prodId: (jShop.record) ? jShop.record['id'] : 0
					}
					,border: false 
				}]
			},{
				title: _('js.images')
				,disabled: (jShop.record) ? false : true
				,items: [{
					xtype: 'jshop-grid-images'
					,url: jShop.config.connectorUrl
					,baseParams: {
						action: 'mgr/images/getlist'
						,prodId: (jShop.record) ? jShop.record['id'] : 0
					}
					,border: false 
				}]
			}]
		}]
    });
    jShop.page.Item.superclass.constructor.call(this,config);
};
Ext.extend(jShop.page.Item,MODx.Component);
Ext.reg('jshop-page-item',jShop.page.Item);

jShop.panel.Header = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'container modx-formpanel'
        ,items: [{
            html: '<h2>'+_('js.management')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        }]
    });
    jShop.panel.Header.superclass.constructor.call(this,config);
};
Ext.extend(jShop.panel.Header,MODx.Panel);
Ext.reg('jshop-panel-header',jShop.panel.Header);
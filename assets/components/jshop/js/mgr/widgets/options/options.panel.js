jShop.panel.Options = function(config) {
    config = config || {};
    Ext.apply(config,{
		url: jShop.config.connectorUrl
		,baseParams: {
			action: (jShop.record) ? 'mgr/items/update' : 'mgr/items/create'
			,id: (jShop.record) ? jShop.record.id : 0
		}
		,layout: 'fit'
		,id: 'jshop-panel-item'
		,border: false
		,defaults: {
			autoHeight: true
			,deferredRender: false
		}
		,deferredRender: false
		,forceLayout: true
		,baseCls: 'modx-formpanel'
		,width: '98%'
		,items: [{
			xtype: 'modx-panel'
			,width: '100%'
			,items: [{
				layout: 'form'
				,cls: 'modx-panel'
				,labelWidth: 175
				,labelAlign: 'top'
				,border: false
				,bodyStyle: { background: 'transparent' ,padding: '10px' }
				,defaults: {
					width: '80%'
					,border: false
					,layout: 'form'
				}
				,items: [{
					layout: 'column'
					,border: false
					,items: [{
						columnWidth: .6
						,border: false
						,layout: 'form'
						,items: [{
							xtype: 'hidden'
							,name: 'id'
						},{
							xtype: 'textfield'
							,name: 'title'
							,fieldLabel: _('js.title')
							,id: 'jshop-item-title'
							,anchor: '100%'
							,listeners: {
								'keyup': {scope:this,fn:function(f,e){
									var title = Ext.util.Format.stripTags(f.getValue());
									Ext.getCmp('jshop-item-page-header').getEl().update('<h2>'+title+'</h2>');
								}}
							}
						},{
							xtype: 'textarea'
							,fieldLabel: _('js.description')
							,name: 'description'
							,anchor: '100%'
						},{
							xtype: 'numberfield'
							,fieldLabel: _('js.price')
							,name: 'price'
							,anchor: '100%'
						},{
							xtype: 'numberfield'
							,name: 'stock'
							,fieldLabel: _('js.stock')
							,minValue: 0
							,allowBlank: false
							,value: '0'
							,anchor: '100%'
						}]
					},{
						columnWidth: .4
						,border: false
						,layout: 'form'
						,items: [{
							xtype: 'combo'
							,displayField: 'name'
							,valueField: 'id'
							,forceSelection: true
							,store: new Ext.data.JsonStore({
								root: 'results'
								,idProperty: 'id'
								,url: jShop.config.connectorUrl
								,baseParams: {
									action: 'mgr/category/getlist'
								}
								,fields: [ 'id','name' ]
							})
							,mode: 'remote'
							,triggerAction: 'all'
							,fieldLabel: _('js.category')
							,name: 'catId'
							,hiddenName: 'catId'
							,allowBlank: false
							,typeAhead: true
							,minChars: 1
							,emptyText: _('js.category_select')
							,valueNotFoundText: _('js.category_err_nf')
							,anchor: '100%'
						},{
							xtype: 'textfield'
							,name: 'alias'
							,fieldLabel: _('js.alias')
							,maxLength: 100
							,anchor: '100%'
						},{
							layout: 'column'
							,border: false
							,items: [{
								columnWidth: .5
								,border: false
								,layout: 'form'
								,items: [{
									xtype: 'combo-boolean'
									,name: 'active'
									,value: (jShop.record) ? jShop.record.active : 1
									,fieldLabel: _('js.active')
									,description: _('js.active_desc')
									,anchor: '100%'
								}]
							},{
								columnWidth: .5
								,border: false
								,layout: 'form'
								,items: [{
									xtype: 'combo-boolean'
									,name: 'featured'
									,value: (jShop.record) ? jShop.record.featured : 0
									,fieldLabel: _('js.featured')
									,description: _('js.featured_desc')
									,anchor: '100%'
								}]
							}]
						}]
					}]
				}]
			}]
		}],
		listeners: {
			'success': function (res) {
                if (jShop.record) {
                    MODx.msg.status({title: _('save_successful'), delay: 3});
                } else {
                    window.location.href = '?a='+MODx.request['a']+'&action=item&id='+res.result.object.id;
                }
            }
        }
    });
    jShop.panel.Options.superclass.constructor.call(this,config);
};
Ext.extend(jShop.panel.Options,MODx.FormPanel);
Ext.reg('jshop-panel-options',jShop.panel.Options);
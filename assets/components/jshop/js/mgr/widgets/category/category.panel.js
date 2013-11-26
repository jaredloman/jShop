jShop.panel.Category = function(config) {
    config = config || {};
    Ext.apply(config,{
		url: jShop.config.connectorUrl
		,baseParams: {
			action: (jShop.record) ? 'mgr/category/update' : 'mgr/category/create'
			,id: (jShop.record) ? jShop.record.id : 0
		}
		,layout: 'fit'
		,id: 'jshop-panel-category'
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
							,fieldLabel: _('js.name')
							,name: 'name'
							,anchor: '100%'
							,listeners: {
								'keyup': {scope:this,fn:function(f,e){
									var title = Ext.util.Format.stripTags(f.getValue());
									Ext.getCmp('jshop-page-category-header').getEl().update('<h2>'+title+'</h2>');
								}}
							}
						},{
							xtype: 'textarea'
							,fieldLabel: _('js.description')
							,name: 'description'
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
							,forceSelection: false
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
							,fieldLabel: _('js.parent')
							,name: 'parent'
							,hiddenName: 'parent'
							,allowBlank: true
							,typeAhead: true
							,minChars: 1
							,emptyText: _('js.category_select')
							,valueNotFoundText: 'None'
							,anchor: '100%'
						},{
							xtype: 'modx-combo-browser'
							,fieldLabel: _('js.category_image')
							,name: 'image'
							,anchor: '100%'
							,source: jShop.config.mediasourceId || 1
							,id: 'supercatimg'
							,hideSourceCombo: true
							,listeners: {
								'select': {fn:function(data) {
									if (Ext.isEmpty(data.thumb)) {
										alert('Dough no data thumb');
										console.log(data);
										//Ext.get('imagePreview').set({src: ''});
									} else {
										alert('Dough yes data thumb');
										console.log(data);
										//Ext.get('imagePreview').set({src: data.thumb});
									}
								},scope:this}
								,'change': {fn:function(cb,nv) {
									alert('Dough we have changed');
									console.log(nv);
									//Ext.get('imagePreview').set({src: nv});
									this.fireEvent('select',{
										relativeUrl: nv
										,url: nv
									});
								},scope:this}
							}
						},{
							xtype: 'hidden'
							,name: 'jshop-panel-image-preview'
							,id: 'hiddenimagepanelpreview'
						}]
					}]
				}/*,{
					xtype: 'textarea'
					,name: 'content'
					,fieldLabel: 'Content'
					,anchor: '100%'
					,height: 100
				}*/]
			}]
		}],
		listeners: {
            'success': function (res) {
                if (jShop.record) {
                    MODx.msg.status({title: _('save_successful'), delay: 3});
                } else {
                    window.location.href = '?a='+MODx.request['a']+'&action=category&id='+res.result.object.id;
                }
            }
        }
    });
    jShop.panel.Category.superclass.constructor.call(this,config);
};
Ext.extend(jShop.panel.Category,MODx.FormPanel);
Ext.reg('jshop-panel-category',jShop.panel.Category);
jShop.grid.Options = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'jshop-grid-options'
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/options/getlist'
		}
		,idProperty: 'id'
		,fields: ['id','name','prodId','index','values']
		,pageSize: 20
		,save_action: 'mgr/options/updateFromGrid'
		,autosave: true
		,enableDragDrop: true
		,ddGroup: 'js-options'
		,ddText: 'Place this row.'
		,viewConfig: {
			emptyText: 'No items found'
			,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
			,forceFit: true
			,autoFill: true
		}
		,autoHeight: true
		,paging: true
		,remoteSort: true
		,columns: [{
			header: _('js.options')
			,dataIndex: 'name'
			,width: 50
			,sortable: false
		},{
			header: _('js.values')
			,dataIndex: 'values'
			,width: 100
			,renderer: this.renderValues
		},{
			header: _('js.sort')
			,dataIndex: 'index'
			,width: 10
			,name: 'index'
			,sortable: true
		}]
		,tbar: [{
			text: _('js.option_add')
			,handler: { xtype: 'jshop-window-option-create' ,prodId: MODx.request.id ,blankValues: true }
		}]
		,listeners: {
			"render": {
			scope: this,
				fn: function(grid) {
					var ddrow = new Ext.dd.DropTarget(grid.container, {
						ddGroup : 'js-options',
						copy:false,
						notifyDrop : function(dd, e, data){
							var ds = grid.store;
							var sm = grid.getSelectionModel();
							var rows = sm.getSelections();
							if(dd.getDragData(e)) {
								var cindex=dd.getDragData(e).rowIndex;
								if(typeof(cindex) != "undefined") {
									for(i = 0; i < rows.length; i++) {
										ds.remove(ds.getById(rows[i].id));
									}
									ds.insert(cindex,data.selections);
									sm.clearSelections();
								}
							}
							var items=[];// read jsons from grid-store-items
							var griddata=ds.data;
							for(i = 0; i < griddata.length; i++) {
								griddata.items[i].json.index = i;
								rowStore = Ext.util.JSON.encode(griddata.items[i].json);
								Ext.Ajax.request({
									url : jShop.config.connectorUrl ,
									params : { action : 'mgr/options/updateFromGrid', data: rowStore },
									method: 'POST',
									success: function ( result, request ) {
										//Ext.MessageBox.alert('Success', 'Data return from the server: '+ result.responseText);
										//console.log(1);
									},
									failure: function ( result, request) {
										//Ext.MessageBox.alert('Failed', result.responseText);
										//console.log(0);
									}
								});
							}
							grid.refresh();
						}
					})
					//this.setWidth('99%');
				}
			}
		}
	});
	jShop.grid.Options.superclass.constructor.call(this,config);
};
Ext.extend(jShop.grid.Options,MODx.grid.Grid,{
    getMenu: function() {
	    return [{
	        text: _('js.option_update')
	        ,handler: this.updateOption
	    },'-',{
	        text: _('js.option_remove')
	        ,handler: this.removeOption
	    }];
	}
	/*,updateOption: function(btn,e) {
		this.updateOptionWindow = MODx.load({
			xtype: 'jshop-window-option-update'
			//,optId: this.menu.record.id
			,listeners: {
				'success': {fn:function() { this.refresh(); },scope:this}
				//,'hide':{fn:function() {this.destroy();},scope:this}
			}
		});
		this.updateOptionWindow.setValues(this.menu.record);
		this.updateOptionWindow.show(e.target);
		var valuesgrid = Ext.getCmp('jshop-grid-option-values');
		valuesgrid.store.load({params:{optId: this.menu.record.id || 0}});
	} */
	,updateOption: function(btn,e) {
        if (!this.updateOptionWindow) {
            this.updateOptionWindow = MODx.load({
                xtype: 'jshop-window-option-update'
                ,record: this.menu.record
				,optId: this.menu.record.id
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }

        this.updateOptionWindow.setValues(this.menu.record);
        this.updateOptionWindow.show(e.target);
        var valuesgrid = Ext.getCmp('jshop-grid-values');
        valuesgrid.store.baseParams = {action: 'mgr/options/values/getlist',optId: this.menu.record.id || 0 ,start: 0 ,limit: valuesgrid.config.pageSize};
		valuesgrid.store.load();
    }
	,removeOption: function() {
		MODx.msg.confirm({
			title: _('js.option_remove')
			,text: _('js.option_remove_confirm')
			,url: this.config.url
			,params: {
				action: 'mgr/options/remove'
				,id: this.menu.record.id
			}
			,listeners: {
				'success': {fn:this.refresh,scope:this}
			}
		});
	}
	,renderValues: function(value, metaData, record, rowIndex, colIndex, store) {
		if (value < 1) {
			return '<span style="font-style:italic;color:#808080">Right Click and Choose "Update Option" to add Values</span>';
		}
		else { return value; }
	}
});
Ext.reg('jshop-grid-options',jShop.grid.Options);

jShop.window.CreateOption = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		title: _('js.options')
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/options/create'
		}
		,layout: 'form'
		,cls: 'modx-panel'
		,bodyStyle: { background: 'transparent' }
		,deferredRender: true
		,autoHeight: true
		,fields: [{
			xtype: 'hidden'
			,name: 'prodId'
			,value: config.prodId
		},{
			xtype: 'textfield'
			,fieldLabel: _('js.option_name')
			,name: 'name'
			,anchor: '100%'
		}]
	});
	jShop.window.CreateOption.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.CreateOption,MODx.Window);
Ext.reg('jshop-window-option-create',jShop.window.CreateOption);

jShop.window.UpdateOption = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		title: _('js.options')
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/options/update'
		}
		,fields: [{
			xtype: 'hidden'
			,name: 'id'
		},{
			xtype: 'textfield'
			,fieldLabel: _('js.option_name')
			,name: 'name'
			,anchor: '100%'
		},{
			xtype: 'jshop-grid-values'
			,fieldLabel: _('js.values')
		}]
	});
	jShop.window.UpdateOption.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.UpdateOption,MODx.Window);
Ext.reg('jshop-window-option-update',jShop.window.UpdateOption);

jShop.grid.Values = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'jshop-grid-values'
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/options/values/getlist'
			,optId: config.optId
		}
		,idProperty: 'id'
		,fields: ['id','name','price','optId','index']
		,paging: true
		,pageSize: 5
		,pageStart: 0
		,showPerPage: true
		,save_action: 'mgr/options/values/updateFromGrid'
		,autosave: true
		,enableDragDrop: true
		,ddGroup: 'js-values'
		,ddText: 'Place this row.'
		,viewConfig: {
			emptyText: 'No items found'
			,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
			,forceFit: true
			,autoFill: true
		}
		,autoHeight: true
		,remoteSort: true
		,columns: [{
			header: _('js.values')
			,dataIndex: 'name'
			,width: 100
		},{
			header: _('js.price')
			,dataIndex: 'price'
			,width: 50
		}]
		,tbar: [{
			text: _('js.value_add')
			//,handler: { xtype: 'jshop-window-value-create' ,blankValues: true }
			,handler: function(btn,e) {
		        if (!this.addValueWindow) {
		            this.addValueWindow = MODx.load({
		                xtype: 'jshop-window-value-create'
						,optId: config.optId
						,listeners: {
							'success': {fn:this.refresh,scope:this}
						}
		            });
		        }
				this.addValueWindow.optId = config.optId;
		        this.addValueWindow.show(e.target);
			}
		}]
		,listeners: {
			"render": {
			scope: this,
				fn: function(grid) {
					/* Temp Test
					grid.store.on('beforeload', function(store, operation, opts){
						operation.params={
							optId: config.optId
						};
					}, this);
					 End Temp Test */
					var ddrow = new Ext.dd.DropTarget(grid.container, {
						ddGroup : 'js-values',
						copy:false,
						notifyDrop : function(dd, e, data){
							var ds = grid.store;
							var sm = grid.getSelectionModel();
							var rows = sm.getSelections();
							if(dd.getDragData(e)) {
								var cindex=dd.getDragData(e).rowIndex;
								if(typeof(cindex) != "undefined") {
									for(i = 0; i < rows.length; i++) {
										ds.remove(ds.getById(rows[i].id));
									}
									ds.insert(cindex,data.selections);
									sm.clearSelections();
								}
							}
							var items=[];// read jsons from grid-store-items
							var griddata=ds.data;
							for(i = 0; i < griddata.length; i++) {
								griddata.items[i].json.index = i;
								rowStore = Ext.util.JSON.encode(griddata.items[i].json);
								Ext.Ajax.request({
									url : jShop.config.connectorUrl ,
									params : { action : 'mgr/options/values/updateFromGrid', data: rowStore },
									method: 'POST',
									success: function ( result, request ) {
										//Ext.MessageBox.alert('Success', 'Data return from the server: '+ result.responseText);
										//console.log(1);
									},
									failure: function ( result, request) {
										//Ext.MessageBox.alert('Failed', result.responseText);
										//console.log(0);
									}
								});
							}
							grid.refresh();
						}
					})
					this.setWidth('99%');
				}
			}
		}
	});
	jShop.grid.Values.superclass.constructor.call(this,config);
};
Ext.extend(jShop.grid.Values,MODx.grid.Grid,{
    getMenu: function() {
	    return [{
	        text: _('js.value_update')
	        ,handler: this.updateValue
	    },'-',{
	        text: _('js.value_remove')
	        ,handler: this.removeValue
	    }];
	},updateValue: function(btn,e) {
		if (this.updateValueWindow) {
            this.updateValueWindow.close();
		}
	        this.updateValueWindow = MODx.load({
	            xtype: 'jshop-window-value-update'
	            ,listeners: {
	                'success': {fn:this.refresh,scope:this}
	            }
	        });
	    this.updateValueWindow.setValues(this.menu.record);
	    this.updateValueWindow.show(e.target);
	},removeValue: function() {
	    MODx.msg.confirm({
	        title: _('js.value_remove')
	        ,text: _('js.value_remove_confirm')
	        ,url: this.config.url
	        ,params: {
	            action: 'mgr/options/values/remove'
	            ,id: this.menu.record.id
	        }
	        ,listeners: {
	            'success': {fn:this.refresh,scope:this}
	        }
	    });
	}	
});
Ext.reg('jshop-grid-values',jShop.grid.Values);

jShop.window.CreateValue = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		title: _('js.value')
		,id: 'jshop-window-value-create'
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/options/values/create'
		}
		,layout: 'form'
		,cls: 'modx-panel'
		,bodyStyle: { background: 'transparent' }
		,deferredRender: true
		,autoHeight: true
		,fields: [{
			xtype: 'hidden'
			,name: 'optId'
			,value: config.optId
		},{
			xtype: 'textfield'
			,fieldLabel: _('js.value')
			,name: 'name'
			,anchor: '100%'
		},{
			xtype: 'numericfield'
			,fieldLabel: _('js.adjustment')
			,name: 'price'
			,anchor: '100%'
		}]
		,listeners : {
			'show' : function(){
				this.baseParams = { action: 'mgr/options/values/create' ,optId: this.optId };
				//console.log(this.baseParams);
	    	}
	  	}
	});
	jShop.window.CreateValue.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.CreateValue,MODx.Window);
Ext.reg('jshop-window-value-create',jShop.window.CreateValue);

jShop.window.UpdateValue = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		title: _('js.values')
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/options/values/update'
		}
		,fields: [{
			xtype: 'hidden'
			,name: 'id'
		},{
			xtype: 'hidden'
			,name: 'optId'
		},{
			xtype: 'textfield'
			,fieldLabel: _('js.value')
			,name: 'name'
			,anchor: '100%'
		},{
			xtype: 'textfield'
			,fieldLabel: _('js.adjustment')
			,name: 'price'
			,anchor: '100%'
		}]
	});
	jShop.window.UpdateValue.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.UpdateValue,MODx.Window);
Ext.reg('jshop-window-value-update',jShop.window.UpdateValue);
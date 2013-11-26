jShop.grid.Values = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/options/values/getlist'
			,start: 0
			,limit: 5
		}
		,id: 'jshop-grid-values'
		,idProperty: 'id'
		,fields: ['id','name','price','optId','index','stock']
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
		},{
			header: _('js.stock')
			,dataIndex: 'stock'
			,width: 50
		}]
		,tbar: [{
			text: _('js.value_add')
			,handler: { xtype: 'jshop-window-value-create' ,blankValues: true }
		}]
		,listeners: {
			"render": {
			scope: this,
				fn: function(grid) {
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
			,optId: MODx.request['id']
		}
		,layout: 'form'
		,cls: 'modx-panel'
		,bodyStyle: { background: 'transparent' }
		,deferredRender: true
		,autoHeight: true
		,fields: [{
			xtype: 'hidden'
			,name: 'optId'
			,value: MODx.request['id']
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
		},{
			xtype: 'textfield'
			,fieldLabel: _('js.stock')
			,name: 'stock'
			,anchor: '100%'
		}]
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
		},{
			xtype: 'textfield'
			,fieldLabel: _('js.stock')
			,name: 'stock'
			,anchor: '100%'
		}]
	});
	jShop.window.UpdateValue.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.UpdateValue,MODx.Window);
Ext.reg('jshop-window-value-update',jShop.window.UpdateValue);
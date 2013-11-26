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
	        text: _('js.values_update')
	        ,handler: this.updateValues
	    },'-',{
	        text: _('js.option_update')
	        ,handler: this.updateOption
	    },'-',{
	        text: _('js.option_remove')
	        ,handler: this.removeOption
	    }];
	}
	,updateValues: function(btn,e) {
		window.location.href = '?a='+MODx.request['a']+'&action=options&id='+this.menu.record.id+'&pid='+MODx.request['id'];
	}
	,updateOption: function(btn,e) {
        if (!this.updateOptionWindow) {
            this.updateOptionWindow = MODx.load({
                xtype: 'jshop-window-option-update'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }

        this.updateOptionWindow.setValues(this.menu.record);
        this.updateOptionWindow.show(e.target);
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
		}]
	});
	jShop.window.UpdateOption.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.UpdateOption,MODx.Window);
Ext.reg('jshop-window-option-update',jShop.window.UpdateOption);
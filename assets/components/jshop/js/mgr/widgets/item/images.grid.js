jShop.grid.Images = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'jshop-grid-images'
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/images/getlist'
		}
		,idProperty: 'id'
		,fields: ['id','image','description','prodId','index']
		,pageSize: 5
		,save_action: 'mgr/images/updateFromGrid'
		,autosave: true
		,enableDragDrop: true
		,ddGroup : 'js-images'
		,ddText : 'Place this row.'
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
			header: _('js.images')
			,dataIndex: 'image'
			,width: 100
			,renderer: this.renderImage
		},{
			header: _('js.description')
			,dataIndex: 'description'
			,width: 250
		},{
			header: _('js.sort')
			,dataIndex: 'index'
			,width: 20
			,name: 'index'
		}]
		,tbar: [{
			text: _('js.image_add')
			,handler: { xtype: 'jshop-window-image-create' ,prodId: MODx.request.id ,blankValues: true }
		}]
		,listeners: {
			"render": {
			scope: this,
				fn: function(grid) {

					// Enable sorting Rows via Drag & Drop
					// this drop target listens for a row drop
					// and handles rearranging the rows

					var ddrow = new Ext.dd.DropTarget(grid.container, {
						ddGroup : 'js-images',
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
								//MODx.fireResourceFormChange();
							}
							var items=[];// read jsons from grid-store-items
							var griddata=ds.data;
							for(i = 0; i < griddata.length; i++) {
								griddata.items[i].json.index = i;
								rowStore = Ext.util.JSON.encode(griddata.items[i].json);
								Ext.Ajax.request({
									url : jShop.config.connectorUrl ,
									params : { action : 'mgr/images/updateFromGrid', data: rowStore },
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

							// ************************************
						}
					})
					this.setWidth('99%');
					//this.syncSize();
					// load the grid store
					// after the grid has been rendered
					//this.store.load();
				}
			}
		}
	});
	jShop.grid.Images.superclass.constructor.call(this,config);
	this.setWidth('99%');
};
Ext.extend(jShop.grid.Images,MODx.grid.Grid,{
    getMenu: function() {
	    return [{
	        text: _('js.image_update')
	        ,handler: this.updateImage
	    },'-',{
	        text: _('js.image_remove')
	        ,handler: this.removeImage
	    }];
	},updateImage: function(btn,e) {
		if (!this.updateImageWindow) {
	        this.updateImageWindow = MODx.load({
	            xtype: 'jshop-window-image-update'
	            ,listeners: {
	                'success': {fn:this.refresh,scope:this}
	            }
	        });
	    }
	    this.updateImageWindow.setValues(this.menu.record);
	    this.updateImageWindow.show(e.target);
	
	},removeImage: function() {
	    MODx.msg.confirm({
	        title: _('js.image_remove')
	        ,text: _('js.image_remove_confirm')
	        ,url: this.config.url
	        ,params: {
	            action: 'mgr/images/remove'
	            ,id: this.menu.record.id
	        }
	        ,listeners: {
	            'success': {fn:this.refresh,scope:this}
	        }
	    });
	},renderImage : function(val, md, rec, row, col, s){
		var source = MODx.config.base_path + jShop.config.mediasourcePath;
		if (val.substr(0,4) == 'http'){
			return '<img style="height:60px" src="' + val + '"/>' ;
		}        
		if (val != ''){
			return '<img src="'+MODx.config.connectors_url+'system/phpthumb.php?h=60&src='+source+val+'&wctx=mgr'+source+'" alt="" />';
		}
		return val;
	}
});
Ext.reg('jshop-grid-images',jShop.grid.Images);

jShop.window.CreateImage = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		title: _('js.images')
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/images/create'
		}
		,layout: 'form'
		,cls: 'modx-panel'
		,bodyStyle: { background: 'transparent' }
		,deferredRender: true
		,autoheight: true
		,fields: [{
			xtype: 'hidden'
			,name: 'prodId'
			,value: config.prodId
		},{
			xtype: 'modx-combo-browser'
			,fieldLabel: _('js.image_name')
			,name: 'image'
			,anchor: '100%'
			,source: jShop.config.mediasourceId
		},{
			xtype: 'textarea'
			,fieldLabel: _('js.image_desc')
			,name: 'description'
			,anchor: '100%'
		}]
	});
	jShop.window.CreateImage.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.CreateImage,MODx.Window);
Ext.reg('jshop-window-image-create',jShop.window.CreateImage);

jShop.window.UpdateImage = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		title: _('js.images')
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/images/update'
		}
		,fields: [{
			xtype: 'hidden'
			,name: 'id'
		},{
			xtype: 'modx-combo-browser'
			,fieldLabel: _('js.image_name')
			,name: 'image'
			,anchor: '100%'
			,source: jShop.config.mediasourceId
		},{
			xtype: 'textarea'
			,fieldLabel: _('js.image_desc')
			,name: 'description'
			,anchor: '100%'
		}]
	});
	jShop.window.UpdateImage.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.UpdateImage,MODx.Window);
Ext.reg('jshop-window-image-update',jShop.window.UpdateImage);
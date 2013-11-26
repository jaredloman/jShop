jShop.grid.Categories = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'jshop-grid-categories'
        ,url: jShop.config.connectorUrl
        ,baseParams: { action: 'mgr/category/getList' }
        ,fields: ['id','name','description','image','parent','index']
        ,paging: true
		,pageSize: 5
		,save_action: 'mgr/category/updateFromGrid'
		,autosave: true
		,enableDragDrop: true
		,ddGroup: 'js-categories'
		,ddText: 'Place this row.'
		,viewConfig: {
			emptyText: 'No items found'
			,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
			,forceFit: true
			,autoFill: true
		}
		,autoHeight: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,columns: [{
			header: _('js.image')
			,dataIndex: 'image'
			,sortable: false
			,width: 60
			,renderer: this.renderImage
		},{
            header: _('js.name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 100
            ,editor: { xtype: 'textfield' }
        },{
            header: _('js.description')
            ,dataIndex: 'description'
            ,sortable: false
            ,width: 100
            ,editor: { xtype: 'textfield' }
        },{
			header: _('js.sort')
			,dataIndex: 'index'
			,width: 20
			,name: 'index'
			,editor: { xtype: 'textfield' }
		}]
		,tbar:[{
		   text: _('js.category_create')
		   ,handler: function() { window.location.href = '?a='+MODx.action['jshop:index']+'&action=category'; }
		}]
		,listeners: {
			"render": {
			scope: this,
				fn: function(grid) {

					// Enable sorting Rows via Drag & Drop
					// this drop target listens for a row drop
					// and handles rearranging the rows

					var ddrow = new Ext.dd.DropTarget(grid.container, {
						ddGroup : 'js-categories',
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
									params : { action : 'mgr/category/updateFromGrid', data: rowStore },
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
					//this.setWidth('99%');
					//this.syncSize();
					// load the grid store
					// after the grid has been rendered
					//this.store.load();
				}
			}
		}
    });
    jShop.grid.Categories.superclass.constructor.call(this,config)
};
Ext.extend(jShop.grid.Categories,MODx.grid.Grid,{
    getMenu: function() {
	    return [{
	        text: _('js.category_update')
	        ,handler: this.updateCategory
	    },'-',{
	        text: _('js.category_remove')
	        ,handler: this.removeCategory
	    }];
	},updateCategory: function(btn,e) {
	    window.location.href = '?a='+MODx.request['a']+'&action=category&id='+this.menu.record.id;
	},removeCategory: function() {
	    MODx.msg.confirm({
	        title: _('js.category_remove')
	        ,text: _('js.category_remove_confirm')
	        ,url: this.config.url
	        ,params: {
	            action: 'mgr/category/remove'
	            ,id: this.menu.record.id
	        }
	        ,listeners: {
	            'success': {fn:this.refresh,scope:this}
	        }
	    });
	},renderImage: function(val, md, rec, row, col, s){
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
Ext.reg('jshop-grid-categories',jShop.grid.Categories);
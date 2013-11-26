jShop.grid.Items = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'jshop-grid-items'
        ,url: jShop.config.connectorUrl
        ,baseParams: { action: 'mgr/items/getList' }
        ,fields: ['id','title','longtitle','description','content','price','options','image','active','featured','catId']
        ,paging: true
		,pageSize: 10
		//,save_action: 'mgr/items/updateFromGrid'
		//,autosave: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'title'
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 20
        },{
			header: _('js.images')
			,dataIndex: 'image'
			,width: 60
			,renderer: this.renderImage
		},{
            header: _('js.title')
            ,dataIndex: 'title'
            ,sortable: true
            ,width: 200
            //,editor: { xtype: 'textfield' }
        },{
            header: _('js.description')
            ,dataIndex: 'description'
            ,sortable: false
            ,width: 250
            //,editor: { xtype: 'textfield' }
        },{
            header: _('js.category')
            ,dataIndex: 'catId'
			,displayField: 'name'
            ,sortable: true
            ,width: 100
            ,editor: {
			    xtype: 'modx-combo'
			    ,renderer: true
			    ,url: jShop.config.connectorUrl    
			    ,baseParams: {
			         action: 'mgr/category/getList'
			    }
			}
        }]
		,tbar:[{
		   text: _('js.item_create')
		   ,handler: function() { 
				window.location.href = '?a='+MODx.action['jshop:index']+'&action=item';
			}
		},'->',{
		    xtype: 'textfield'
		    ,id: 'jshop-search-filter'
		    ,emptyText: _('js.search...')
		    ,listeners: {
		        'change': {fn:this.search,scope:this}
		        ,'render': {fn: function(cmp) {
		            new Ext.KeyMap(cmp.getEl(), {
		                key: Ext.EventObject.ENTER
		                ,fn: function() {
		                    this.fireEvent('change',this);
		                    this.blur();
		                    return true;
		                }
		                ,scope: cmp
		            });
		        },scope:this}
		    }
		},'-',{
            xtype: 'modx-combo'
		    ,renderer: true
		    ,url: jShop.config.connectorUrl    
		    ,baseParams: {
		         action: 'mgr/category/getList'
		    }
            ,emptyText: _('js.category_select')
            ,id: 'jshop-category-filter'
            ,width: 200
            ,listeners: {
                'select': {fn: this.filterByCategory, scope: this}
            }
        },'-',{
		   xtype: 'button',
		   text: 'Clear Filter',
		   listeners: {
		   	'click': {fn: this.clearFilter, scope: this}
		   }
		}]
		
    });
    jShop.grid.Items.superclass.constructor.call(this,config)
};
Ext.extend(jShop.grid.Items,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
	,filterByCategory: function (cb, rec, ri) {
        this.getStore().baseParams['catId'] = rec.data['id'];
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
	,clearFilter: function() {
        this.getStore().baseParams['query'] = '';
        this.getStore().baseParams['catId'] = '';
        Ext.getCmp('jshop-search-filter').reset();
        Ext.getCmp('jshop-category-filter').reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
	,getMenu: function() {
	    return [{
	        text: _('js.item_update')
	        ,handler: this.updateItem
	    },'-',{
	        text: _('js.item_remove')
	        ,handler: this.removeItem
	    }];
	}
	,updateItem: function(btn,e) {
		window.location.href = '?a='+MODx.request['a']+'&action=item&id='+this.menu.record.id;
	}
	,removeItem: function() {
	    MODx.msg.confirm({
	        title: _('js.item_remove')
	        ,text: _('js.item_remove_confirm')
	        ,url: this.config.url
	        ,params: {
	            action: 'mgr/items/remove'
	            ,id: this.menu.record.id
	        }
	        ,listeners: {
	            'success': {fn:this.refresh,scope:this}
	        }
	    });
	}
	,renderImage : function(val, md, rec, row, col, s){
		var source = MODx.config.base_path + jShop.config.mediasourcePath;
		if (val.substr(0,4) == 'http'){
			return '<img style="height:60px" src="' + val + '"/>' ;
		}        
		if(val == 'NO'){
			return '<img src="'+MODx.config.connectors_url+'system/phpthumb.php?h=60&src='+jShop.config.assetsPath+'images/default.png&wctx=mgr'+jShop.config.assetsPath+'" alt="No Image" />';
		}
		if (val != ''){
			return '<img src="'+MODx.config.connectors_url+'system/phpthumb.php?h=60&src='+source+val+'&wctx=mgr'+source+'" alt="" />';
		}
		return val;
	}
});
Ext.reg('jshop-grid-items',jShop.grid.Items);
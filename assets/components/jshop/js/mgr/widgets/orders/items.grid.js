jShop.grid.Items = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'jshop-grid-items'
		,url: jShop.config.connectorUrl
        ,baseParams: { 
			action: 'mgr/orders/getItemList' 
			,id: config.oid
		}
        ,fields: ['pid','title','options','quantity','price']
        ,paging: true
		,pageSize: 10
		,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'options'
        ,columns: [{
            header: _('js.id')
            ,dataIndex: 'pid'
            ,sortable: true
            ,width: 30
        },{
            header: _('js.order_name')
            ,dataIndex: 'title'
            ,sortable: true
            ,width: 125
        },{
            header: _('js.order_options')
            ,dataIndex: 'options'
            ,sortable: false
            ,width: 150
			,renderer: {fn: this._renderOptions,scope:this}
			//,renderer: {fn:this._renderAddress,scope:this}
        },{
            header: _('js.order_quantity')
            ,dataIndex: 'quantity'
            ,sortable: true
            ,width: 30
        },{
           header: _('js.order_price')
           ,dataIndex: 'price'
           ,sortable: true
           ,width: 60
		   //,renderer: {fn:this._renderTotal,scope:this}
           //,editor: { xtype: 'textfield' }
        }]
    });
    jShop.grid.Items.superclass.constructor.call(this,config);
	//this._makeTemplates();
};
Ext.extend(jShop.grid.Items,MODx.grid.Grid,{
    _makeTemplates: function() {
        this.tplOptions = new Ext.XTemplate('<tpl for="."><div class="order-address-column">'
										    +'<p class="main-column"><span class="order-address">{address}, </span><span class="order-city">{city}, </span><span class="order-state">{state} </span><span class="order-zip">{zip}</span></p>'
											+'</tpl>',{
			compiled: true
		});
    }
	,_renderOptions:function(v,md,rec) {
		var oa = [];
		var opts = Ext.util.JSON.decode(rec.data.options);
		for (var key in opts) {
		  if (opts.hasOwnProperty(key)) {
			var ok = opts[key]; 
			oa.push(ok.name +': '+ok.value+' ');
		  }
		}
		return oa.join(",");
	}
});
Ext.reg('jshop-grid-items',jShop.grid.Items);
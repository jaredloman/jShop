jShop.grid.Orders = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'jshop-grid-orders'
        ,url: jShop.config.connectorUrl
        ,baseParams: { action: 'mgr/orders/getList' }
        ,fields: ['id','custId','email','products','name','oname','address','addr','city','state','zip','phone','deliveryMethod','paymentMethod','comments','subtotal','shipping','tax','taxrate','total','status','trackingno','createdon','editedon']
        ,paging: true
		,pageSize: 10
		,save_action: 'mgr/orders/updateFromGrid'
		,autosave: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'name'
        ,columns: [{
            header: _('js.order_id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 60
        },{
            header: _('js.order_name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 125
            //,editor: { xtype: 'textfield' }
        },{
            header: _('js.order_address')
            ,dataIndex: 'address'
            ,sortable: false
            ,width: 125
			,renderer: {fn:this._renderAddress,scope:this}
            //,editor: { xtype: 'textfield' }
        },{
            header: _('js.order_trackingno')
            ,dataIndex: 'trackingno'
            ,sortable: false
            ,width: 125
            ,editor: { xtype: 'textfield' }
        },{
           header: _('js.order_total')
           ,dataIndex: 'total'
           ,sortable: true
           ,width: 60
			,renderer: {fn:this._renderTotal,scope:this}
           //,editor: { xtype: 'textfield' }
        },{
           header: _('js.order_status')
           ,dataIndex: 'status'
           ,sortable: true
           ,width: 100
           ,editor: {
		    	xtype: 'modx-combo'
		    	,renderer: true
		    	,url: jShop.config.connectorUrl    
		    	,baseParams: {
		         action: 'mgr/status/getList'
		    	}
			}
        },{
			header: _('js.order_createdon')
			,dataIndex: 'createdon'
			,sortable: true
			,width: 100
		}]
		,tbar:[{
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
		},'->',{
			xtype: 'datefield'
			,emptyText: _('js.filterOrderStart')
			,id: 'jshop-orderstart-filter'
			,listeners: {
				select: {fn:this.filterOrderStart, scope: this}
				,scope: this
			}
			,width: 200
		},'-',{
			xtype: 'datefield'
			,emptyText: _('js.filterOrderEnd')
			,id: 'jshop-orderend-filter'
			,listeners: {
				select: {fn:this.filterOrderEnd, scope: this}
				,scope: this
			}
			,width: 200
		},'-',{
			xtype: 'modx-combo'
			,renderer: true
			,url: jShop.config.connectorUrl    
			,baseParams: {
				action: 'mgr/status/getList'
			}
			,emptyText: _('js.order_status_select')
			,id: 'jshop-status-filter'
			,width: 200
			,listeners: {
				'select': {fn: this.filterByStatus, scope: this}
			}
        },'-',{
		   xtype: 'button',
		   text: 'Clear Filter',
		   listeners: {
		   	'click': {fn: this.clearFilter, scope: this}
		   }
		}]
		
    });
    jShop.grid.Orders.superclass.constructor.call(this,config);
	this._makeTemplates();
};
Ext.extend(jShop.grid.Orders,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },filterOrderStart: function(tf) {
        var s = this.getStore();
        s.baseParams.ostart = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },filterOrderEnd: function(tf) {
        var s = this.getStore();
        s.baseParams.oend = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },filterByStatus: function (cb, rec, ri) {
        this.getStore().baseParams['status'] = rec.data['id'];
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },clearFilter: function() {
        this.getStore().baseParams['query'] = '';
        this.getStore().baseParams['status'] = '';
		this.getStore().baseParams['ostart'] = '';
		this.getStore().baseParams['oend'] = '';
        Ext.getCmp('jshop-search-filter').reset();
        Ext.getCmp('jshop-status-filter').reset();
		Ext.getCmp('jshop-orderstart-filter').reset();
		Ext.getCmp('jshop-orderend-filter').reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },getMenu: function() {
		var m = [];
	    m.push({
	        text: _('js.order_update')
	        ,handler: this.updateOrder
			,scope: this
	    }/*,'-',{
        text: _('js.order_remove')
        ,handler: this.removeOrder
	    }*/);
		if (this.menu.record.custId >= 1) {
			m.push('-',{
		        text: _('js.user_update')
		        ,handler: this.updateUser
		    });
		}
		return m;
	},updateOrder: function(btn,e) {
		if (!this.updateOrderWindow) {
            this.updateOrderWindow = MODx.load({
                xtype: 'jshop-window-order-update'
                ,record: this.menu.record
				,oid: this.menu.record.id
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }
		Ext.getCmp('sendStatus').setValue(false);
		this.updateOrderWindow.record = this.menu.record;
        this.updateOrderWindow.setValues(this.menu.record);
        this.updateOrderWindow.show(e.target);
        var itemsgrid = Ext.getCmp('jshop-grid-items');
        itemsgrid.store.baseParams = {action: 'mgr/orders/getitemlist',id: this.menu.record.id || 0 ,start: 0 ,limit: itemsgrid.config.pageSize};
		itemsgrid.store.load();
	},updateUser: function(btn,e) {
		MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/customers/get'
                ,id: this.menu.record.custId
                ,getGroups: false
            }
            ,listeners: {
                'success': {fn:function(r) {
					if(!this.updateUserWindow) {
						this.updateUserWindow = MODx.load({
							xtype: 'jshop-window-customer'
							,user: this.menu.record.custId
							,listeners: {
								'success': {fn: function() {
									this.refresh;
									console.log(this);
								},scope:this}
							}
						});
					}
					this.updateUserWindow.record = r.object;
					this.updateUserWindow.setValues(r.object);
					this.updateUserWindow.show();
					var uogrid = Ext.getCmp('jshop-grid-user-orders');
			        uogrid.store.baseParams = {action: 'mgr/orders/getlist',uid: this.menu.record.custId || 0 ,start: 0 ,limit: uogrid.config.pageSize};
					uogrid.store.load();
                },scope:this}
				,'failure': {fn:function(r){
					Ext.Msg.hide();
		            Ext.Msg.show({
		                title: _('js.customer_password_notification')
		                ,msg: r.message
						,buttons: Ext.Msg.OK
		            });
				},scope:this}
            }
        });
	},removeOrder: function() {
	    MODx.msg.confirm({
	        title: _('js.order_remove')
	        ,text: _('js.order_remove_confirm')
	        ,url: jShop.config.connectorUrl
	        ,params: {
	            action: 'mgr/orders/remove'
	            ,id: this.menu.record.id
	        }
	        ,listeners: {
	            'success': {fn:this.refresh,scope:this}
	        }
	    });
	},_makeTemplates: function() {
		this.tplTotal = new Ext.XTemplate('<tpl for=".">'
            +'<div class="order-total-column"><span class="currency">$</span><span class="order-total">{total}</span></div>'
        +'</tpl>',{
			compiled: true
		});
        this.tplAddress = new Ext.XTemplate('<tpl for="."><div class="order-address-column">'
										    +'<p class="main-column"><span class="order-address">{address}, </span><span class="order-city">{city}, </span><span class="order-state">{state} </span><span class="order-zip">{zip}</span></p>'
											+'</tpl>',{
			compiled: true
		});
    }
	,_renderAddress:function(v,md,rec) {
		return this.tplAddress.apply(rec.data);
	}
	,_renderTotal:function(v,md,rec) {
		return this.tplTotal.apply(rec.data);
	}
});
Ext.reg('jshop-grid-orders',jShop.grid.Orders);

jShop.window.UpdateOrder = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		id: 'jshop-window-update-order'
		,title: _('js.order_update')
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/orders/update'
		}
		,width: '800'
		,fields: [{
			xtype: 'hidden'
			,name: 'id'
			,id: 'js-oid'
		},{
			xtype: 'modx-tabs'
			,autoHeight: true
			,deferredRender: false
			,forceLayout: true
			,border: true
			,defaults: {
				border: false
				,autoHeight: true
				,layout: 'form'
				,deferredRender: false
				,forceLayout: false
				,cls: 'main-wrapper'
			}
			,items: [{
				title: _('js.order_info')
				,items: [{
					layout: 'column'
					,items: [{
						columnWidth: .6
						,border:true
						,layout: 'form'
						,items: [{
							xtype: 'fieldset'
							,title: _('js.order_shippinginfo')
							,items: [{
								xtype: 'statictextfield'
								,fieldLabel: _('js.order_name')
								,name: 'oname'
								,anchor: '100%'
							},{
								xtype: 'statictextfield'
								,fieldLabel: _('js.order_address')
								,name: 'addr'
								,anchor: '100%'
							},{
								xtype: 'statictextfield'
								,fieldLabel: _('js.order_phone')
								,name: 'phone'
								,anchor: '100%'
							},{
								xtype: 'statictextfield'
								,fieldLabel: _('js.order_deliveryMethod')
								,name: 'deliveryMethod'
								,anchor: '100%'
							}]
						}]
					},{
						columnWidth: .4
						,border: true
						,layout: 'form'
						,items: [{
							xtype: 'fieldset'
							,title: _('js.order_totals')
							,items: [{
								xtype: 'statictextfield'
								,fieldLabel: _('js.order_subtotal')
								,name: 'subtotal'
								,anchor: '100%'
							},{
								xtype: 'statictextfield'
								,fieldLabel: _('js.order_shipping')
								,name: 'shipping'
								,anchor: '100%'
							},{
								xtype: 'statictextfield'
								,fieldLabel: _('js.order_tax')
								,name: 'tax'
								,anchor: '100%'
							},{
								xtype: 'statictextfield'
								,fieldLabel: _('js.order_total')
								,name: 'total'
								,anchor: '100%'
							}]
						}]
					}]
				},{
					xtype: 'fieldset'
					,title: _('js.order_items')
					,items: [{
						xtype: 'jshop-grid-items'
					}]
				}]
			},{
				title: _('js.order_edit')
				,items: [{
					layout: 'column'
					,items: [{
						columnWidth: .6
						,border: true
						,layout: 'form'
						,items: [{
							xtype: 'fieldset'
							,title: _('js.order_shippinginfo')
							,items: [{
								xtype: 'textfield'
								,fieldLabel: _('js.order_name')
								,name: 'name'
								,anchor: '100%'
							},{
								xtype: 'textfield'
								,fieldLabel: _('js.order_address')
								,name: 'address'
								,anchor: '100%'
							},{
								xtype: 'textfield'
								,fieldLabel: _('js.order_city')
								,name: 'city'
								,anchor: '100%'
							},{
								xtype: 'modx-combo-state'
								,fieldLabel: _('js.order_state')
								,name: 'state'
								,anchor: '100%'
							},{
								xtype: 'textfield'
								,fieldLabel: _('js.order_zip')
								,name: 'zip'
								,anchor: '100%'
							}]
						}]
					},{
						columnWidth: .4
						,border: true
						,layout: 'form'
						,items: [{
							xtype: 'fieldset'
							,title: _('js.order_stattrack')
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
										action: 'mgr/status/getlist'
									}
									,fields: [ 'id','name' ]
								})
								,mode: 'remote'
								,triggerAction: 'all'
								,fieldLabel: _('js.status')
								,name: 'status'
								,hiddenName: 'status'
								,allowBlank: false
								,typeAhead: false
								,minChars: 1
								,emptyText: _('js.status_select')
								,valueNotFoundText: _('js.status_notfound')
								,anchor: '100%'
							},{
								xtype: 'xcheckbox'
								,id: 'sendStatus'
								,name: 'sendStatus'
								,boxLabel: 'Send status update email'
								,checked: false
							},{
								xtype: 'textfield'
								,fieldLabel: _('js.order_trackingno')
								,name: 'trackingno'
								,anchor: '100%'
							}]
						},{
							xtype: 'fieldset'
							,title: _('js.order_totals')
							,items: [{
								xtype: 'button'
								,text: _('js.order_override_totals')
								,handler: this.overrideTotals
								,scope: this
							}]
						}]
					}]
				},{
					layout: 'column'
					,items: [{
						columnWidth: 1
						,border: true
						,layout: 'form'
						,items: [{
							xtype: 'fieldset'
							,title: _('js.order_comments')
							,items: [{
								xtype: 'textarea'
								,name: 'comments'
								,anchor: '100%'
							}]
						}]
					}]
				}]
				
			}]
		}]
	});
	jShop.window.UpdateOrder.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.UpdateOrder,MODx.Window,{
	overrideTotals: function(btn,e) {
		if (this.overrideTotalsWindow) {
			this.overrideTotalsWindow.reset();
		}
		this.overrideTotalsWindow = MODx.load({
			xtype: 'jshop-window-override-totals'
			,record: this.record
			,scope: this
		});
		this.overrideTotalsWindow.setValues(this.record);
        this.overrideTotalsWindow.show(e.target);
    }
});
Ext.reg('jshop-window-order-update',jShop.window.UpdateOrder);

jShop.window.OverrideTotals = function(config) {
	config = config || {};
	Ext.applyIf(config,{
		title: _('js.order_override_totals')
		,url: jShop.config.connectorUrl
		,baseParams: {
			action: 'mgr/orders/update'
		}
		,fields: [{
			xtype: 'hidden'
			,name: 'id'
		},{
			xtype: 'hidden'
			,name: 'taxrate'
		},{
			xtype: 'numberfield'
			,fieldLabel: _('js.order_subtotal')
			,name: 'subtotal'
			,anchor: '100%'
			,listeners: {
				'change': {fn:this.calculateFields,scope:this}
			}
		},{
			xtype: 'numberfield'
			,fieldLabel: _('js.order_tax')
			,name: 'tax'
			,anchor: '100%'
			,readOnly: true
			,cls: 'x-item-disabled'
		},{
			xtype: 'numberfield'
			,fieldLabel: _('js.order_shipping')
			,name: 'shipping'
			,anchor: '100%'
			,listeners: {
				'change': {fn:this.calculateFields,scope:this}
			}
		},{
			xtype: 'numberfield'
			,fieldLabel: _('js.order_total')
			,name: 'total'
			,anchor: '100%'
		}]
		,listeners: {
			'success': {fn: this.refreshParents,scope:this}
		}
	});
	jShop.window.OverrideTotals.superclass.constructor.call(this,config);
};
Ext.extend(jShop.window.OverrideTotals,MODx.Window,{
	calculateFields: function() {
		var fp = this.fp.getForm();
		var values = fp.getValues();
		var tax = parseFloat(values.subtotal) * parseFloat(values.taxrate);
		tax = tax.toFixed(2);
		fp.findField("tax").setValue(tax);
		var total = parseFloat(values.subtotal) + parseFloat(tax) + parseFloat(values.shipping);
		fp.findField("total").setValue(total);
	}
	,refreshParents: function(frm) {
		//console.log(frm);
		var win = Ext.getCmp('jshop-window-update-order');
		var fp = win.fp.getForm();
		fp.setValues(frm.a.result.object);
		var grid = Ext.getCmp('jshop-grid-orders').refresh();
	}
});
Ext.reg('jshop-window-override-totals',jShop.window.OverrideTotals);
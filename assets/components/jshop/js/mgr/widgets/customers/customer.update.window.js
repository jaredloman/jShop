jShop.window.Customer = function(config) {
    config = config || {};
    config.id = config.id || Ext.id(),
    Ext.applyIf(config,{
        title: _('js.customer_update'),
        autoHeight: true,
        url: jShop.config.connectorUrl,
        baseParams: {
            action: 'mgr/customers/update'
        },
        width: 600,
        fields: [{
			xtype: 'hidden',
			name: 'id'
		},{
			id: 'modx-user-newpassword'
			,name: 'newpassword'
			,xtype: 'hidden'
			,value: false
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
			,items: this.getFields(config)
		}],
		listeners: {
            'setup': {fn:this.setup,scope:this},
            'success': {fn:this.success,scope:this},
        },
        keys: [] //prevent enter
    });
    jShop.window.Customer.superclass.constructor.call(this,config);
    //Ext.getCmp('modx-user-password-genmethod-s').on('check',this.showNewPassword,this);
};
Ext.extend(jShop.window.Customer,MODx.Window,{
	success: function(o) {
		console.log(o);
		var userId = this.config.user;
        if (Ext.getCmp('modx-user-passwordnotifymethod-s').getValue() === true && o.result.message != '') {
            Ext.Msg.hide();
            Ext.Msg.show({
                title: _('js.customer_password_notification')
                ,msg: o.result.message
                ,buttons: Ext.Msg.OK
                ,fn: function(btn) {
                    return false;
                }
            });
        } else if (userId == 0) {
            Ext.Msg.show({
				title: 'Uh oh!'
				,msg: 'There was an error loading the user. Please try again.'
				,buttons: Ext.Msg.OK
				,fn: function(btn) {
					return false;
				}
			});
        }
    }
	,showNewPassword: function(cb,v) {
        var el = Ext.getCmp('modx-user-panel-newpassword').getEl();
        if (v) {
            el.slideIn('t',{useDisplay:true});
        } else {
            el.slideOut('t',{useDisplay:true});
        }
    }
	,getFields: function(config) {
		var f = [];
		f.push({
		    title: _('js.customer_main'),
		    items: [{
		        xtype: 'textfield',
		        name: 'fullname',
		        fieldLabel: _('js.customer_name'),
		        anchor: '100%',
		        allowBlank: false,
		        maxLength: 126
		    },{
		        xtype: 'textfield',
		        name: 'username',
		        fieldLabel: _('js.customer_username'),
		        anchor: '100%',
		        allowBlank: false,
		        maxLength: 100
		    },{
		        xtype: 'textfield',
		        name: 'email',
		        fieldLabel: _('js.customer_email'),
		        vtype: 'email',
		        anchor: '100%',
		        allowBlank: false,
		        maxLength: 126
		    },{
		        id: 'modx-user-fs-newpassword'
		        ,title: _('js.customer_new_password')
		        ,xtype: 'fieldset'
		        ,checkboxToggle: true
		        ,collapsed: true
		        ,forceLayout: true
		        ,listeners: {
		            'expand': {fn:function(p) {
		                Ext.getCmp('modx-user-newpassword').setValue(true);
		                //this.markDirty();
		            },scope:this}
		            ,'collapse': {fn:function(p) {
		                Ext.getCmp('modx-user-newpassword').setValue(false);
		                //this.markDirty();
		            },scope:this}
		        }
				,items: [{
		            xtype: 'radiogroup'
		            ,fieldLabel: _('js.customer_password_method')
		            ,columns: 1
		            ,items: [{
		                id: 'modx-user-passwordnotifymethod-e'
		                ,name: 'passwordnotifymethod'
		                ,boxLabel: _('js.customer_password_method_email')
		                ,xtype: 'radio'
		                ,value: 'e'
		                ,inputValue: 'e'
		            },{
		                id: 'modx-user-passwordnotifymethod-s'
		                ,name: 'passwordnotifymethod'
		                ,boxLabel: _('js.customer_password_method_screen')
		                ,xtype: 'radio'
		                ,value: 's'
		                ,inputValue: 's'
		                ,checked: true
		            }]
		        },{
		            xtype: 'radiogroup'
		            ,fieldLabel: _('js.customer_password_gen_method')
		            ,columns: 1
		            ,items: [{
		                id: 'modx-user-password-genmethod-g'
		                ,name: 'passwordgenmethod'
		                ,boxLabel: _('js.customer_password_gen_gen')
		                ,xtype: 'radio'
		                ,inputValue: 'g'
		                ,value: 'g'
		                ,checked: true
		            },{
		                id: 'modx-user-password-genmethod-s'
		                ,name: 'passwordgenmethod'
		                ,boxLabel: _('js.customer_password_gen_specify')
		                ,xtype: 'radio'
		                ,inputValue: 'spec'
		                ,value: 'spec'
						,listeners: {
							'check': {fn: function() {
					            Ext.getCmp('modx-user-panel-newpassword').getEl().toggle();
					        },scope:this}
						}
		            }]
		        },{
		            id: 'modx-user-panel-newpassword'
		            ,xtype: 'fieldset'
		            ,border: false
		            ,autoHeight: true
					,style: 'display:none'
		            ,items: [{
		                id: 'modx-user-specifiedpassword'
		                ,name: 'specifiedpassword'
		                ,fieldLabel: _('js.customer_change_password_new')
		                ,xtype: 'textfield'
		                ,inputType: 'password'
		                ,anchor: '100%'
		            },{
		                id: 'modx-user-confirmpassword'
		                ,name: 'confirmpassword'
		                ,fieldLabel: _('js.customer_change_password_confirm')
		                ,xtype: 'textfield'
		                ,inputType: 'password'
		                ,anchor: '100%'
		            }]
		        }]
		    }]
		},{
		    title: _('js.customer_details'),
		    items: [{
		        xtype: 'textarea',
		        name: 'address',
		        fieldLabel: _('js.customer_address'),
		        anchor: '100%'
		    },{
		        layout: 'column',
		        items: [{
		            columnWidth: .5,
		            layout: 'form',
		            items: [{
		                xtype: 'textfield',
		                name: 'zip',
		                fieldLabel: _('js.customer_zip'),
		                anchor: '100%'
		            },{
		                xtype: 'modx-combo-state',
		                name: 'state',
		                fieldLabel: _('js.customer_state'),
		                anchor: '100%'
		            },{
		                xtype: 'textfield',
		                name: 'phone',
		                fieldLabel: _('js.customer_phone'),
		                anchor: '100%'
		            }]
		        },{
		            columnWidth: .5,
		            layout: 'form',
		            items: [{
		                xtype: 'textfield',
		                name: 'city',
		                fieldLabel: _('js.customer_city'),
		                anchor: '100%'
		            },{
		                xtype: 'textfield',
		                name: 'country',
		                fieldLabel: _('js.customer_country'),
		                anchor: '100%'
		            },{
		                xtype: 'textfield',
		                name: 'mobilephone',
		                fieldLabel: _('js.customer_mobilephone'),
		                anchor: '100%'
		            }]
		        }]
		    }]
		},{
			title: _('js.customer_orders')
			,items: [{
				xtype: 'jshop-grid-user-orders'
				,url: jShop.config.connectorUrl
				,baseParams: {
					action: 'mgr/orders/getlist'
					,uid: config.user
				}
			}]
		});
		return f;
	}
});
Ext.reg('jshop-window-customer',jShop.window.Customer);

jShop.grid.UserOrders = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'jshop-grid-user-orders'
        ,url: jShop.config.connectorUrl
        ,baseParams: { 
			action: 'mgr/orders/getList' 
		}
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
		    ,id: 'jshop-user-search-filter'
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
		   xtype: 'button',
		   text: 'Clear Filter',
		   listeners: {
		   	'click': {fn: this.clearFilter, scope: this}
		   }
		}]
		
    });
    jShop.grid.UserOrders.superclass.constructor.call(this,config);
	this._makeTemplates();
};
Ext.extend(jShop.grid.UserOrders,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },clearFilter: function() {
        this.getStore().baseParams['query'] = '';
        Ext.getCmp('jshop-user-search-filter').reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    },getMenu: function() {
		var m = [];
	    m.push({
	        text: _('js.order_update')
	        ,handler: this.updateOrder
			,scope: this
	    });
		return m;
	}
	,updateOrder: function(btn,e) {
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
		this.updateOrderWindow.record = this.menu.record;
        this.updateOrderWindow.setValues(this.menu.record);
        this.updateOrderWindow.show(e.target);
        var itemsgrid = Ext.getCmp('jshop-grid-items');
        itemsgrid.store.baseParams = {action: 'mgr/orders/getitemlist',id: this.menu.record.id || 0 ,start: 0 ,limit: itemsgrid.config.pageSize};
		itemsgrid.store.load();
	}
	,_makeTemplates: function() {
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
Ext.reg('jshop-grid-user-orders',jShop.grid.UserOrders);

/**
 * Displays a state combo
 * 
 * @class MODx.combo.State
 * @extends Ext.form.ComboBox
 * @param {Object} config An object of configuration properties
 * @xtype modx-combo-state
 */
MODx.combo.State = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [['',0],['Alabama','AL'],['Montana','MT'],['Alaska','AK'],['Nebraska','NE'],['Arizona','AZ'],['Nevada','NV'],['Arkansas','AR'],['New Hampshire','NH'],['California','CA'],['New Jersey','NJ'],['Colorado','CO'],['New Mexico','NM'],['Connecticut','CT'],['New York','NY'],['Delaware','DE'],['North Carolina','NC'],['Florida','FL'],['North Dakota','ND'],['Georgia','GA'],['Ohio','OH'],['Hawaii','HI'],['Oklahoma','OK'],['Idaho','ID'],['Oregon','OR'],['Illinois','IL'],['Pennsylvania','PA'],['Indiana','IN'],['Rhode Island','RI'],['Iowa','IA'],['South Carolina','SC'],['Kansas','KS'],['South Dakota','SD'],['Kentucky','KY'],['Tennessee','TN'],['Louisiana','LA'],['Texas','TX'],['Maine','ME'],['Utah','UT'],['Maryland','MD'],['Vermont','VT'],['Massachusetts','MA'],['Virginia','VA'],['Michigan','MI'],['Washington','WA'],['Minnesota','MN'],['West Virginia','WV'],['Mississippi','MS'],['Wisconsin','WI'],['Missouri','MO'],['Wyoming','WY']]
        })
        ,displayField: 'd'
        ,valueField: 'v'
        ,mode: 'local'
        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
    });
    MODx.combo.State.superclass.constructor.call(this,config);
};
Ext.extend(MODx.combo.State,Ext.form.ComboBox);
Ext.reg('modx-combo-state',MODx.combo.State);

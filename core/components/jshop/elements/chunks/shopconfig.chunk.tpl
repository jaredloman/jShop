<script type="text/javascript" src="[[++site_url]]assets/js/plum-latest.js"></script>
<script type="text/javascript" src="[[++site_url]]assets/js/plum.fn.shop.js"></script>
<script type="text/javascript">
Shop.checkout.stripe = function (custom) {
	Stripe.setPublishableKey('[[++sm.test_publishable]]');
	function stripeResponseHandler(status, response) {
        if (response.error) {
            // re-enable the submit button
			_(".submit-button").attr('disabled', null, false);
            // show the errors on the form
			_(".payment-errors").html(response.error.message);
        } else {
            // insert the response data so it gets submitted to the server
			formdata.stripeToken = response.id;
			formdata.last4 = response.card.last4;
			formdata.ctype = response.card.type;
			formdata.deliveryMethod = deliveryMethod;
			
			if (formdata.savecustomer['on'] == '1') { formdata.savecustomer = true; } else if (formdata.savecustomer['on'] == false) { formdata.savecustomer = false; } else if (formdata.savecustomer == 'false') { formdata.savecustomer = false; } else { formdata.savecustomer = 'broken'; }
			if (formdata.savePayment['on'] == '1') { formdata.savePayment = true; } else if (formdata.savePayment['on'] == false) { formdata.savePayment = false; } else { formdata.savePayment = false; }
			if (formdata.different_shipping['on'] == '1') { formdata.different_shipping = true; } else if (formdata.different_shipping['on'] == false) { formdata.different_shipping = '0'; } else { formdata.different_shipping = 'broken'; }

			_.each(formdata, function(key,value) { if (value == '') { delete formdata[key]; } });
			
			var post = _.merge({ items: cart_items, subtotal: subtotal, shipping: shipping, tax: tax, total: total }, formdata);
			master.checkout('[[~23]]',post);
        }
    }
	
	_(".submit-button").attr("disabled", "disabled");
	
	/* Create global variables to pass cart data */
	var cart_items = JSON.stringify(this.cart.items);
	var subtotal = this.subtotal;
	var shipping = this.shipping;
	var tax = this.tax;
	var total = this.total;
	var formdata = this.cart.form;
	var deliveryMethod = this.cart.shipping;
	
	var master = this;

	_.each(formdata.scId, function(key,value) { 
		if (value == true) { 
			formdata.scid = key;
		}
	});
	
	if (formdata.hasOwnProperty('scid')) {
		formdata.deliveryMethod = deliveryMethod;
		formdata.savePayment = false;
		if (formdata.savecustomer['on'] == '1') { formdata.savecustomer = true; } else if (formdata.savecustomer['on'] == false) { formdata.savecustomer = false; } else if (formdata.savecustomer == 'false') { formdata.savecustomer = false; } else { formdata.savecustomer = 'broken'; }
		if (formdata.different_shipping['on'] == '1') { formdata.different_shipping = true; } else if (formdata.different_shipping['on'] == false) { formdata.different_shipping = '0'; } else { formdata.different_shipping = 'broken'; }

		_.each(formdata, function(key,value) { if (value == '') { delete formdata[key]; } });
		var post = _.merge({ items: cart_items, subtotal: subtotal, shipping: shipping, tax: tax, total: total }, formdata);
		master.checkout('[[~23]]',post);
	} else {
	    Stripe.createToken({
			number: _('.card-number').value(),
	        cvc: _('.card-cvc').value(),
	        exp_month: _('.card-expiry-month').value(),
	        exp_year: _('.card-expiry-year').value()
	     }, stripeResponseHandler);	
	}
};
_('#cart').shop({
	cartitem: '<span class="sect title">{title}<br /><span class="options">{all}</span></span> '
		+ '<span class="sect quantity"><input class="shop-quantity" value="{quantity}"></span>'
		+ '<span class="sect priceunit">{pricesingle}</span> '
		+ '<span class="sect pricetotal">{pricetotal}</span> '
		+ '<span class="sect"><a class="shop-remove" href="#">Remove <span class="icon remove-icon">X</span></a></span>',
	currencyFormat: '$00,000,000.00', // £
	currencyCode: 'USD',
	generateSKU: true,
	geolocation: true,
	tax: { 'US:WA': [ 0.084 ] },
	sandbox: true,
	shippingType: 'range,flat',
	shippingProp: 'price',
	shipping: {
		'*': {
			'USPS Flate Rate': { rates: { "0.01": 5.95, "20.00": 6.95, "40.00": 7.95, "60.00": 8.95, "75.00": 0.00 } },
			'Local Pickup': { rates: { "0.01": 0.00, "100.00": 0.00 } }
		}
	},
	shortCodes: {
		all: function (product) {
		    var s = this, o = [];
		    _.each(product, function (name, value) {
		        if (!s.properties.test(name) && name != 'options' && name != 'pid') {
					o.push(name + ': '+ value);
		        }
		    });
			return o.join('<br />');
		}
	},
	paypaluser: 'debbra_1356057086_biz',
	paypaldomain: 'me.com',
	form: '#vpCheckout',
	addAfter: function () {
		alert("You've added a new item to your cart!");
	},
	emptyCartBefore: function () {
		return confirm(
			'This message is controlled by a callback function.\n\n'
			+ 'Are you sure you want to empty your cart?'
		);
	},
});

// Dynamicly set Taxes
_('#state').on('change', function () {
	if (Shop.cart.form.different_shipping['on'] == '0') {
		Shop.cart.region = this.value;
		Shop.update();
	}
});

_('#different_shipping').on('change', function() {
	if (Shop.cart.form.different_shipping['on'] == '1') {
		if (_('#shipping_state').value() != '') {
			Shop.cart.region = _('#shipping_state').value();
			params = {addr: _('#shipping_address').value(), city: _('#shipping_city').value(), zip: _('#shipping_zip').value()};
		}
	} else {
		if (_('#state').value() != '') {
			Shop.cart.region = _('#state').value();
			params = {addr: _('#address').value(), city: _('#city').value(), zip: _('#zip').value()};
		}
	}
	if (Shop.cart.region == 'WA') {
		_.ajax({
			url: '[[~31]]',
			method: 'GET',
			params: params,
			complete: function (opts, xhr, parse) {
				options = JSON.parse(opts);
				Shop.options.tax['US:WA'][0] = options['@attributes'].rate;
				Shop.update();
			}
		});
	}
	Shop.update();
});

_('#shipping_state').on('change', function () {
	Shop.cart.region = this.value;
	Shop.update();
});

_('#zip').on('change', function () {
	if (Shop.cart.form.different_shipping['on'] == '0') {
		if (_('#state').value() == 'WA') {
			_.ajax({
				url: '[[~31]]',
				method: 'GET',
				params: { addr: _('#address').value(), city: _('#city').value(), zip: this.value },
				complete: function (opts, xhr, parse) {
					options = JSON.parse(opts);
					Shop.options.tax['US:WA'][0] = options['@attributes'].rate;
					Shop.update();
				}
			});
		}	
	}
});
_('#shipping_zip').on('change', function () {
	if (Shop.cart.form.different_shipping['on'] == '1') {
		if (_('#shipping_state').value() == 'WA') {
			_.ajax({
				url: '[[~31]]',
				method: 'GET',
				params: { addr: _('#shipping_address').value(), city: _('#shipping_city').value(), zip: this.value },
				complete: function (opts, xhr, parse) {
					options = JSON.parse(opts);
					Shop.options.tax['US:WA'][0] = options['@attributes'].rate;
					Shop.update();
				}
			});
		}	
	}
});
var pdoptions;
_('select.pdOption').each(function () {
	pdoptions = pdoptions || {};
	theopt = _(this);
	tn = theopt.attr('name');
	tv = theopt.value();
	ti = this.options[this.selectedIndex].getAttribute('data-shop-id');
	pdoptions[tn] = { id: ti, name: tn, value: tv };
});
_('#shop-options').value(JSON.stringify(pdoptions));
_('select.pdOption').on('change', function () {
	pdoptions = pdoptions || {};
	opt = _(this);
	pdname = opt.attr('name');
	pdvalue = opt.value();
	pdid = this.options[this.selectedIndex].getAttribute('data-shop-id');
	pdoptions[pdname] = { id: pdid, name: pdname, value: pdvalue }; 
	//console.log(pdoptions);
	_('#shop-options').value(JSON.stringify(pdoptions));
});
</script>
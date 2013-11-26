<h4><span class="t-dark">PAYMENT</span> INFO</h4>
<fieldset id="paymentInfo">
	<div class="form-row">
		<label>Card Number</label>
		<input type="text" size="20" autocomplete="off" class="card-number"/><span class="payment-errors error"></span>
	</div>
	<div class="form-row">
		<label>CVC</label>
		<input type="text" size="4" autocomplete="off" class="card-cvc"/>
	</div>
	<div class="form-row">
		<label>Expiration (MM/YYYY)</label>
		<input type="text" size="2" class="card-expiry-month"/>
		<span> / </span>
		<input type="text" size="4" class="card-expiry-year"/>
	</div>
	<div class="form-row">
	<label for="savePayment">Securely Save Card<span class="error">[[!+fi.error.savePayment]]</span></label>
	<input type="checkbox" class="checkbox" name="savePayment" id="savePayment" />
	</div>
</fieldset>
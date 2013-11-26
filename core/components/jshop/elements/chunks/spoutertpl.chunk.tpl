[[!FormIt? &hooks=`js.removePaymentMethod` &submitVar=`removeCC` &validate=`customerId:required,userId:required` &successMessage=`Successfully Removed Saved Payment Method!` ]]
[[!+fi.successMessage:notempty=`<p class="success centered">[[!+fi.successMessage]]</p>`]]
[[!+fi.error_message:notempty=`<p class="error centered">There was an error removing the credit card. Please try again.</p>`]]
<ul id="mySavedPaymentMethods">
  <li class="head"><span class="ctype-head">Card Type</span><span class="last4-head">Last 4</span><span class="deleteCard-head">Remove</span></li>
  [[+wrapper]]
</ul>
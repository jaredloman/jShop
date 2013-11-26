<h1>ORDER RECEIPT</h1>
<h4>Dear [[+name]]</h4>
<p>Thank you for ordering from VapoLife.com! We really appreciate your business! Your order details are shown below. If anything looks incorrect, please <a href="mailto:info@vapolife.com">email us</a> or call us as soon as possible!</p>
<h2>Order Details</h2>
<h3>Customer Info</h3>
<table border="0">
<tr>
  <td><strong>Order ID:</strong></td>
  <td>[[+id]]</td>
</tr>
<tr>
  <td><strong>Customer Name:</strong></td>
  <td>[[+cust_name]]</td>
</tr>
<tr>
  <td><strong>Customer Address:</strong></td>
  <td>[[+cust_address]],[[+cust_city]],[[+cust_state]] [[+cust_zip]]</td>
</tr>
<tr>
  <td><strong>Customer Phone:</strong></td>
  <td>[[+phone]]</td>
</tr>
</table>
<h3>Shipping Info</h3>
<table border="0">
	<tr>
		<td><strong>Delivery Method:</strong></td>
		<td>[[+deliveryMethod]]</td>
	</tr>
	<tr>
	  <td><strong>Shipping Name:</strong></td>
	  <td>[[+name]]</td>
	</tr>
	<tr>
	  <td><strong>Shipping Address:</strong></td>
	  <td>[[+address]],[[+city]],[[+state]] [[+zip]]</td>
	</tr>
</table>
<h3>Products Ordered</h3>
[[+items]]
<br />
<br />
<h3>Order Totals</h3>
<table>
	<tr>
		<td><strong>Subtotal:</strong></td>
		<td>[[+subtotal:formatPrice]]</td>
	</tr>
	<tr>
		<td><strong>Tax:</strong></td>
		<td>[[+tax:formatPrice]]</td>
	</tr>
	<tr>
		<td><strong>Shipping:</strong></td>
		<td>[[+shipping:formatPrice]]</td>
	</tr>
	<tr>
		<td><strong>Total:</strong></td>
		<td>[[+total:formatPrice]]</td>
	</tr>
</table>
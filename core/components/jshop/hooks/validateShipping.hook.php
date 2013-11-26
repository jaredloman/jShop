<?php
/**
* jShop
*
* Copyright 2013 by Jared Loman <jared@jaredloman.com>
*
* This file is part of jShop, a simple shopping component for MODx Revolution.
*
* jShop is free software; you can redistribute it and/or modify it under the
* terms of the GNU General Public License as published by the Free Software
* Foundation; either version 2 of the License, or (at your option) any later
* version.
*
* jShop is distributed in the hope that it will be useful, but WITHOUT ANY
* WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
* A PARTICULAR PURPOSE. See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with
* jShop; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
* Suite 330, Boston, MA 02111-1307 USA
*
* @package jshop
*/
/**
* @package jshop
* @subpackage hooks
*/

/* Setup the variables */
$shipping = $hook->getValue('shipping');
$total = $hook->getValue('total');
$deliveryMethod = $hook->getValue('deliveryMethod');
$ds = $hook->getValue('different_shipping');
$vals = array('Name'=>'shipping_name','Address'=>'shipping_address','City'=>'shipping_city','State'=>'shipping_state','Zip'=>'shipping_zip');
$errorMsg = ' is a required field';
$set = array();

/* First let's make sure a shipping option was selected */
if ($total < 75 && $deliveryMethod != 'Local Pickup' && $shipping < 1) {
	$hook->addError('shipping','Shipping'.$errorMsg);
	$set['shipping'] = 'error';
}

/* Now let's test to see if things will be shipped to a different shipping address. */

if ($ds === 'true') {
	foreach ($vals as $k => $v) {
		$val = $hook->getValue($v);

		if (empty($val)) {
			$set[$v] = 'error';
			$hook->addError($v,$k.$errorMsg);
		}
		unset($val);
	}
}
if (in_array("error",$set)) {
	return false;
} else { 
	return true; 
}
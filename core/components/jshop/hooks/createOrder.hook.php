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
* EasyInventory; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
* Suite 330, Boston, MA 02111-1307 USA
*
* @package jshop
*/
/**
* @package jshop
* @subpackage hooks
*/
$js = $modx->getService('jshop','jShop',$modx->getOption('js.core_path',null,$modx->getOption('core_path').'components/jshop/').'model/jshop/',$scriptProperties);
if (!($js instanceof jShop)) return '';

/* Get Customer ID if Set */
$cid = $hook->getValue('custId') ? $hook->getValue('custId') : (int)0;

/* Create Order Object */
$order = $modx->newObject('jsOrder');

$oa = $hook->getValues();

/* Check for separate Shipping Address */
if ($hook->getValue('different_shipping') == true) {
	$skip = array("name","address","city","state","zip","spam","id");
} else { 
	$skip = array("spam","id"); 
}

foreach ($oa as $k=>$v) {
	if (!in_array($k,$skip)){
		$order->set($k, $v);
	}
} 

if ($hook->getValue('different_shipping') == true) {
	$order->set('name',$hook->getValue('shipping_name'));
	$order->set('address',$hook->getValue('shipping_address'));
	$order->set('city',$hook->getValue('shipping_city'));
	$order->set('state',$hook->getValue('shipping_state'));
	$order->set('zip',$hook->getValue('shipping_zip'));
} else {
	$order->set('name', $hook->getValue('fullname'));
}
$order->set('createdon',time());
$order->set('createdby',$cid);

/* Get Default Status */
$sq = $modx->newQuery('jsStatus');
$sq->where(array('default' => 1));
$status = $modx->getObject('jsStatus',$sq);

if($status) { $order->set('status',$status->get('id')); } else { $hook->addError('error_message','Could not get Status Object'); return false; }

/* Set product info */
$order->set('products',$hook->getValue('items'));
//$order->set('deliveryMethod',null); // Temporary Fudge to pass method // Why did I do this?
//$order->set('paymentMethod',null); // Temporary Fudge to pass method // Why did I do this?

if ($order->save()) {
	$hook->setValue('orderId',$order->get('id'));
	return true; 
} else {
	$hook->addError('error_message','Could not create the order object'); 
	return false; 
}
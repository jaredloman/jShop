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

$prices = array();
$total = 0;
$carttotal = $hook->getValue('total');
$items = $hook->getValue('items');
if(!$items) {
	$hook->addError('error_message','You Have no products in your cart.'); 
	return false;
}

$items = $modx->fromJSON($items);

if (count($items) >= 1) {
	foreach ($items as $item) {
		$i = $modx->getObject('jsItem',intval($item['pid']));
		if (!empty($i)) {
			$prices[] = intval($i->get('price'));
		} else { $hook->addError('error_message','Could not get item object with the id: '.$item['pid']); return false; }
	}
} else {
	$hook->addError('error_message','Could not retrieve items from JSON'); return false;
}

if(count($prices) >= 1) {
	foreach ($prices as $price) {
		$total = $total + $price;
	}
	if($carttotal < $total) {
		$hook->addError('error_message','Something didn\'t quite add up... I\'d suggest starting over!'); return false;
	} else {
		return true;
	}
} else {
	$hook->addError('error_message','Unable to retrieve item prices for validation'); return false;
}
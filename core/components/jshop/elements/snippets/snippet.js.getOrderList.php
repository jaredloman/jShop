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
* @subpackage snippet
*/
$output = '';

$js = $modx->getService('jshop','jShop',$modx->getOption('js.core_path',null,$modx->getOption('core_path').'components/jshop/').'model/jshop/',$scriptProperties);
if (!($js instanceof jShop)) return '';

/* setup default properties */
$orderlisttpl = $modx->getOption('orderlisttpl',$scriptProperties,'orderlisttpl');
$orderlistoutertpl = $modx->getOption('orderlistoutertpl',$scriptProperties,'orderlistoutertpl');
$orderdisplaytpl = $modx->getOption('orderdisplaytpl',$scriptProperties,'orderdisplaytpl');
$displayoptionstpl = $modx->getOption('displayoptionstpl',$scriptProperties,'displayoptionstpl');
$displayoptionsoutertpl = $modx->getOption('displayoptionsoutertpl',$scriptProperties,'displayoptionsoutertpl');
$displayproductrowtpl = $modx->getOption('displayproductrowtpl',$scriptProperties,'displayproductrowtpl');
$displayproductoutertpl = $modx->getOption('displayproductoutertpl',$scriptProperties,'displayproductoutertpl');
$hideIncomplete = $modx->getOption('hideIncomplete',$scriptProperties,0);
$sort = $modx->getOption('sort',$scriptProperties,'createdon');
$dir = $modx->getOption('dir',$scriptProperties,'DESC');
$idx = !empty($idx) ? $idx : (integer)1;
$limit = $modx->getOption('limit',$scriptProperties,10);
$offset = $modx->getOption('offset',$scriptProperties,0);
$totalVar = $modx->getOption('totalVar', $scriptProperties, 'total');

$loggedin = $modx->user->isAuthenticated($modx->context->get('key'));
$user = $modx->user;
$profile = $user->getOne('Profile');

if(isset($_REQUEST['o'])) $oid = $_REQUEST['o'];
// Check for FURLS
$furls = $modx->getOption('friendly_urls',$scriptProperties,0);

$o = "";

if(!empty($oid)) { 	
	$order = $modx->getObject('jsOrder', $oid);
	if(!$order) { return 'Order not found with ID of: '.$oid; }
	
	// Setup Customer Info
	if (intval($order->get('custId')) === $modx->user->id) {
		if ($profile) {
			$cname = $profile->get('fullname');
			$caddress = $profile->get('address');
			$ccity = $profile->get('city');
			$cstate = $profile->get('state');
			$czip = $profile->get('zip');	
		} else {
			$modx->log(xPDO::LOG_LEVEL_ERROR,'Order List: Could not get profile for user id: '.$modx->user->id);
			return 'Error retrieving Profile with id: '.$modx->user->id;
		}
	} else {
		$cname = $order->get('name');
		$caddress = $order->get('address');
		$ccity = $order->get('city');
		$cstate = $order->get('state');
		$czip = $order->get('zip');
	}
	
	$order->set('cust_name',$cname);
	$order->set('cust_address',$caddress);
	$order->set('cust_city',$ccity);
	$order->set('cust_state',$cstate);
	$order->set('cust_zip',$czip);
	
	// Get Products
	$products = $order->get('products');
	$items = $modx->fromJSON($products);
	if ($items && count($items) >= 1) {
		$ps = '';
		$oo = '';
		$os = '';
		$vs = array();
		foreach ($items as $item) {
			$theopts = $item['options'];
			$opts = $modx->fromJSON($theopts);
			if ($opts && count($opts) >= 1) {
				foreach ($opts as $key => $val) {
					$os.= $js->getChunk($displayoptionstpl,array('name'=>$key,'value'=>$val['value']));
				}
				$oo .= $js->getChunk($displayoptionsoutertpl,array('wrapper'=>$os));
				$item['options'] = $oo;
			}
			$ps .= $js->getChunk($displayproductrowtpl,$item);
		}
		$po = $js->getChunk($displayproductoutertpl,array('wrapper'=>$ps));
		unset($ps);
		$order->set('items',$po);
	} else {
		$modx->log(xPDO::LOG_LEVEL_ERROR,'Send Email: Could not get product list or there were not products: '.$products);
	}
	$order = $order->toArray();
	$o .= $js->getChunk($orderdisplaytpl,$order);
	
	return $o;
} else {
	if ($loggedin && $user) {
		$ol = "";
		$uid = $modx->user->id;
		$c = $modx->newQuery('jsOrder');
		$c->where(array('custId' => (int)$uid));
		$c->sortby($sort,$dir);
		if($hideIncomplete == 1) $c->where(array('status:NOT IN' => array(1,2,8)));
		//return print_r($c->toSQL($c->prepare()));
		$total = $modx->getCount('jsOrder',$c);
		$modx->setPlaceholder($totalVar,$total);
		$c->limit($limit,$offset);
		$orders = $modx->getCollection('jsOrder', $c);

		if($total < 1) { 
			$o = $js->getChunk($orderlistoutertpl,array('wrapper'=>'No Orders Found for the user id: '.$uid));
		} else {
			foreach ($orders as $order) {
				$order = $order->toArray();
				$ol.= $js->getChunk($orderlisttpl,$order);
			}
			$o = $js->getChunk($orderlistoutertpl,array('wrapper'=>$ol));
		}

		return $o;
	}
}
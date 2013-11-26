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
$mode = $modx->getOption('js.mode',$scriptProperties,1);
if ($mode == 1) { $secretkey = $modx->getOption('js.test_secret',$scriptProperties,'nokeyset'); } 
else { $secretkey = $modx->getOption('js.live_secret',$scriptProperties,'nokeyset'); }
if ($secretkey == 'nokeyset') { $hook->addError('error_message','Please set API Keys in System Settings'); return false; }

$js = $modx->getService('jshop','jShop',$modx->getOption('js.core_path',null,$modx->getOption('core_path').'components/jshop/').'model/jshop/',$scriptProperties);
if (!($js instanceof jShop)) return '';

/* Initialize Stripe */
require_once ($modx->getOption('js.core_path',null,$modx->getOption('core_path').'components/jshop/').'model/payment/lib/Stripe.php');

Stripe::setApiKey($secretkey);

/* Get Fields for creating a charge */
$cid = $hook->getValue('custId');
$oid = $hook->getValue('orderId');
$ctype = $hook->getValue('ctype');
$last4 = $hook->getValue('last4');
$scid = $hook->getValue('scid');

$token = $hook->getValue('stripeToken');
$email = $hook->getValue('email');

$amount = $hook->getValue('total'); // Use transID to help prevent Script Kiddies from quickly adjusting the total
$amount = $amount * (int)100; // Convert the amount to cents for Stripe

/* Create Stripe Customer */
if ($cid > 0 && $oid > 0) {
	
	if (!$scid) {
		$customer = Stripe_Customer::create(array(
		  "card" => $token,
		  "email" => $email)
		);

		/* Generate Stripe Customer ID */
		$scid = $customer->id;	
	}
	
	if ($scid) {
		
		if ($hook->getValue('savePayment') == true) {
			/* Get User */
			$user = $modx->getObject('modUser',(int)$cid);
			if(!$user) { $hook->addError('error_message','Could not get User with ID of: '.$cid); return false; }
			/* Set Profile Fields */
			$userprof = $user->getOne('Profile');
			if(!empty($userprof)){
				$extended = array();
				$extended = $userprof->get('extended');

				if(!empty($ctype) && !empty($last4)) {
					$exArray = array('customerId' => $scid,'ctype' => $ctype,'last4' => $last4);
				} else { 
					$exArray = array('customerId' => $scid); 
				}
				$extended['card'][] = $exArray;

				$userprof->set('extended', $extended);	
			} else { $hook->addError('error_message','Could not get User Profile'); return false; }

			$userprofresult = $userprof->save();

			if (!$userprofresult) {
				$hook->addError('error_message','Could not update the User Profile');
				return false;
			}
		}
		
		try {
			// charge the Customer instead of the card
			$charge = Stripe_Charge::create(array(
			  "amount" => $amount, # amount in cents, again
			  "currency" => "usd",
			  "customer" => $scid,
			  "description" => $oid)
			);
			
			$response = json_decode($charge);
			
			if ($response->paid == true && $oid > 0) {
				/* Set Order Status */
				$order = $modx->getObject('jsOrder',$oid);
				if(!empty($order)) {
					$order->set('status',(int)2);
					$paid = $order->save();
				} else { $hook->addError('error_message','The order was processed but payment failed for some reason.'); return false; }
				if($paid) { return true; } else { $hook->addError('error_message','Could not successfully charge the card'); return false; }
			}
		}
		catch (Exception $e) {
			$hook->addError('error_message','There was an issue with charging your card. The error was: ' . $e->getMessage());
			return false;
		}
	} else { 
		$hook->addError('error_message','Could not generate a Stripe Customer.'); 
		return false; 
	}

} elseif ($oid > 0) {
	$charge = Stripe_Charge::create(array(
	  "amount" => $amount, // amount in cents, again
	  "currency" => "usd",
	  "card" => $token,
	  "description" => $oid)
	);
	if ($charge) {
		/* Set Order Status */
		$order = $modx->getObject('jsOrder',$oid);
		if(!empty($order)) {
			$order->set('status',(int)2);
			$saved = $order->save();
		} else { 
			$hook->addError('error_message','Failed retrieving the order: '.$oid); 
			return false; 
		}
		if($saved) { return true; } else { $hook->addError('error_message','Could not successfully change the order status'); return false; }
	} else { $hook->addError('error_message','Could not successfully authorize the card'); return false; }
	return true;
} else {
	$hook->addError('error_message','Something failed, please contact us by phone'); 
	return false; 
}
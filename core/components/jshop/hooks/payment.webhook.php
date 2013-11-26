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

$time = date("F j, Y, g:i a");

/* Initialize Stripe */
require_once ($modx->getOption('js.core_path',null,$modx->getOption('core_path').'components/jshop/').'model/payment/lib/Stripe.php');

Stripe::setApiKey($secretkey);

// retrieve the request's body and parse it as JSON
$body = @file_get_contents('php://input');
$event_json = json_decode($body);

function setStatus($o,$response=8,$sid) {
	global $modx;
	$response = (int)$response;
	$o->set('status',$response);
	$o->set('stripeId',$sid);
	if(!$o->save()) {
		$modx->log(xPDO::LOG_LEVEL_ERROR,'Set Status: Could not set status of order with the status code: '.$response);
	}
}

function adjustStock($o) {
	global $modx;
	$products = $o->get('products');
	$items = $modx->fromJSON($products);
	if ($items && count($items) >= 1) {
		foreach ($items as $item) {
			$sku = $item['id'];
			preg_match('^sku-(.*?)\:^',$sku,$match);
			$pid = intval($match[1]);
			if ($pid >= 1) {
				$i = $modx->getObject('jsItem',$pid);
				if ($i) {
					$stock = $i->get('stock');
					$stock--;
					$i->set('stock',$stock);
					if (!$i->save()) {
						$modx->log(xPDO::LOG_LEVEL_ERROR,'Adjust Stock: Could not adjust the stock of the item with the id: '.$pid);
					}
				} else {
					$modx->log(xPDO::LOG_LEVEL_ERROR,'Adjust Stock: Could not get item with the id: '.$pid);
				}
			} else {
				$modx->log(xPDO::LOG_LEVEL_ERROR,'Adjust Stock: Could not retrieve the id from the sku: '.$sku.'. It looked like this though: '.$pid);
			}
			$theopts = $item['options'];
			$opts = $modx->fromJSON($theopts);
			if ($opts && count($opts) >= 1) {
				foreach ($opts as $opt) {
					$oid = $opt['id'];
					if ($oid) {
						$ov = $modx->getObject('jsOptionVals',$oid);
						$ostock = $ov->get('stock');
						$ostock--;
						$ov->set('stock',$ostock);
						if (!$ov->save()) {
							$modx->log(xPDO::LOG_LEVEL_ERROR,'Adjust Stock: Could not adjust the stock of the option with the id: '.$oid.' to a value of: '.$ostock);
						}
					} else { 
						$modx->log(xPDO::LOG_LEVEL_ERROR,'Adjust Stock: Could not get option with the id: '.$oid);
					}
				}
			}
		}
	} else {
		$modx->log(xPDO::LOG_LEVEL_ERROR,'Adjust Stock: Could not get list of items from: '.$products);
	}
}

function emailReceipt($o) {
	global $modx;
	
	$js = $modx->getService('jshop','jShop',$modx->getOption('js.core_path',null,$modx->getOption('core_path').'components/jshop/').'model/jshop/',$scriptProperties);
	if (!($js instanceof jShop)) return '';
	
	/* Get order ID for other params */
	$id = $o->get('id');
	
	/* Snippet Properties */
	$orderReceiptTpl = $modx->getOption('orderReceiptTpl',$scriptProperties,'orderReceiptTpl');
	$orderProductRowTpl = $modx->getOption('orderProductRowTpl',$scriptProperties,'orderProductRowTpl');
	$orderProductOuterTpl = $modx->getOption('orderProductOuterTpl',$scriptProperties,'orderProductOuterTpl');
	$orderOptionsRowTpl = $modx->getOption('orderOptionsRowTpl',$scriptProperties,'orderOptionsRowTpl');
	$orderOptionsOuterTpl = $modx->getOption('orderOptionsOuterTpl',$scriptProperties,'orderOptionsOuterTpl');
	$orderOptionsChildTpl = $modx->getOption('orderOptionsChildTpl',$scriptProperties,'orderOptionsChildTpl');
	$fromName = $modx->getOption('site_name',$scriptProperties,'Store');
	$emailStoreOwner = $modx->getOption('emailStoreOwner',$scriptProperties,true);
	$sitename = $modx->getOption('site_name',$scriptProperties,'site_name');
	$storeOwner = $modx->getOption('storeOwner',$scriptProperties,$sitename);
	$emailsender = $modx->getOption('emailsender',$scriptProperties,NULL);
	$subject = $modx->getOption('subject',$scriptProperties,$fromName.' - New Store Order: '.$id);
	$storeEmailTo = $modx->getOption('storeEmailTo',$scriptProperties,$emailsender);
	$storeOwnerReceiptTpl = $modx->getOption('storeOwnerReceiptTpl',$scriptProperties,'storeOwnerReceiptTpl');
		
	/* Setup Customer Variables */
	$name = $o->get('name');
	$address = $o->get('address');
	$city = $o->get('city');
	$state = $o->get('state');
	$zip = $o->get('zip');
	$email = $o->get('email');	
	
	$cid = $o->get('custId');
	if ($cid >= 1) {
		$user = $modx->getObject('modUser',$cid);
		$internalkey = $user->get('id');
		$profile = $user->getOne('Profile',array('internalKey'=>$internalkey));
		$name = $profile->get('fullname');
		$address = $profile->get('address');
		$city = $profile->get('city');
		$state = $profile->get('state');
		$zip = $profile->get('zip');
	}
	$o->set('cust_name',$name);
	$o->set('cust_address',$address);
	$o->set('cust_city',$city);
	$o->set('cust_state',$state);
	$o->set('cust_zip',$zip);
	/* Setup Products & Options */
	$products = $o->get('products');
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
					$os.= $js->getChunk($orderOptionsRowTpl,array('name'=>$key,'value'=>$val['value']));
				}
				$oo .= $js->getChunk($orderOptionsOuterTpl,array('wrapper'=>$os));
				unset($os);
				$item['options'] = $oo;
				unset($oo);
			}
			$ps .= $js->getChunk($orderProductRowTpl,$item);
		}
		$po = $js->getChunk($orderProductOuterTpl,array('wrapper'=>$ps));
		unset($ps);
		$o->set('items',$po);
	} else {
		$modx->log(xPDO::LOG_LEVEL_ERROR,'Send Email: Could not get product list or there were not products: '.$products);
	}
	/* Lets Start Mailing */
	$order = $o->toArray();
	$message = $js->getChunk($orderReceiptTpl,$order);

	/* now load modMail, and setup options */
	$modx->getService('mail', 'mail.modPHPMailer');
	$modx->mail->set(modMail::MAIL_BODY,$message);
	$modx->mail->set(modMail::MAIL_FROM,$emailsender);
	$modx->mail->set(modMail::MAIL_FROM_NAME,$fromName);
	$modx->mail->set(modMail::MAIL_SENDER,$emailsender);
	$modx->mail->set(modMail::MAIL_SUBJECT,$subject);
	$modx->mail->address('reply-to',$emailsender);
	$modx->mail->setHTML(true);

	/* specify the recipient */
	$modx->mail->address('to',$email);
	

	/* send! */
	if (!$modx->mail->send()) {
		$modx->log(xPDO::LOG_LEVEL_ERROR,'Send Email: Could not send email receipt to address: '.$email.'. The error was: '.$modx->mail->mailer->ErrorInfo);
	}
	$modx->mail->reset();
	if ($emailStoreOwner) {
		$o->set('storeOwner',$storeOwner);
		$sorder = $o->toArray();
		$storemessage = $modx->getChunk($storeOwnerReceiptTpl,$sorder);
		$modx->getService('mail', 'mail.modPHPMailer');
		$modx->mail->set(modMail::MAIL_BODY,$storemessage);
		$modx->mail->set(modMail::MAIL_FROM,$emailsender);
		$modx->mail->set(modMail::MAIL_FROM_NAME,$fromName);
		$modx->mail->set(modMail::MAIL_SENDER,$emailsender);
		$modx->mail->set(modMail::MAIL_SUBJECT,$subject);
		$modx->mail->address('reply-to',$emailsender);
		$modx->mail->setHTML(true);
		$modx->mail->address('to',$emailStoreOwner);
		if(!$modx->mail->send()) {
			$modx->log(xPDO::LOG_LEVEL_ERROR,'Send Email: Could not send email to store owner at the address: '.$emailStoreOwner);	
		}
		$modx->mail->reset();
	}
}

try {
    // for extra security, retrieve from the Stripe API, this will fail in Test Webhooks
    $event_id = $event_json->{'id'};
    $event = Stripe_Event::retrieve($event_id);
    
    if ($event->type == 'charge.succeeded') {
		$oid = $event->data->object['description'];
		$order = $modx->getObject('jsOrder',(int)$oid);
		if(is_object($order)) {
			setStatus($order,3,$event->data->object['id']);
			adjustStock($order);
			emailReceipt($order);
		} else {
			$modx->log(xPDO::LOG_LEVEL_ERROR,'Payment Webhook could not get order with id: '.$oid);
		}
    }
        
} catch (Stripe_InvalidRequestError $e) { 
	$modx->log(xPDO::LOG_LEVEL_ERROR,'Payment Webhook: '.$e->getMessage());   
}
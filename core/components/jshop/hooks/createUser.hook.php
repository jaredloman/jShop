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

/* Get Fields for creating a customer */
$savecustomer = $hook->getValue('savecustomer');
$groupid = $modx->getOption('js.group_id',$scriptProperties,(int)1);

/* Setup Variables for Customer Info */
$fullname = $hook->getValue('fullname');
$address = $hook->getValue('address');
$city = $hook->getValue('city');
$state = $hook->getValue('state');
$zip = $hook->getValue('zip');
$phone = $hook->getValue('phone');
$email = $hook->getValue('email');

/* Create a Secure Password */
$rpass = substr(md5(md5(time().$hook->getValue('email').rand(111111,999999))),0,12);

if($savecustomer == true) {
	
	/* Check if username exists */
	$exists = $modx->getObject('modUser',array('username' => $email)) ? true : false;
	
	if($exists === false) {
		/* Create Array of User Fields */
		$cinfo = array(
		    'fullname' => $fullname,
		    'address' => $address,
		    'city' => $city,
			'state' => $state,
		    'zip' => $zip,
			'phone' => $phone,
		    'email' => $email,
		);
		
		/* Create User & Profile */
		$user = $modx->newObject('modUser', array ('username'=> $email,'password' => $rpass,'active' => true,'sudo' => false));
		
		/* Save User */
		$userSaved = $user->save();
		
		if(!$userSaved) {
			$hook->addError('error_message','Could not save user by the name of: '.$email.'. Please contact us by phone.');
			return false;
		} else {
			
			/* We are good.. now let's create a profile */
			
			$userProfile = $modx->newObject('modUserProfile');
			$userProfile->set('internalKey',$user->get('id'));
			$userProfile->fromArray($cinfo);
			
			/* Save Profile */
			$profileSaved = $userProfile->save();
			
			if (!$profileSaved) {
				$hook->addError('error_message','Could not successfully save the user profile. Please contact us by phone.');
				return false;
			} else {
				
				/* We are good...now lets add them to the correct group */
				
				/* Add user to Group */
				$user = $modx->getObject('modUser',array('username' => $email));
				if (!$user) {
					$hook->addError('error_message','Could not get user object'); 
					return false;
				} else { 
					/* We have the user...move on */
					$internalkey = $user->get('id');
					$profile = $user->getOne('Profile',array('internalKey'=>$internalkey));
					$user->joinGroup((int)$groupid);
					if(!$user->save()){
						$hook->addError('error_message','Could Not Join User Group');
						return false;
					} else {
						/* The user is in the correct group! Now let's email them */ 
						$hook->setValue('custId',$user->get('id'));
						
						if (isset($rpass)) {
					        $phs = array_merge($modx->config,$user->toArray(),array('password' => $rpass),array('fullname' => $hook->getValue('fullname')));
					        $mail = $modx->getChunk('registrationMail',$phs); //@todo Make configurable
					        $options = array(
					            'subject' => 'Thanks for Registering!' // @todo Make configurable
					        );

					        $sent = $user->sendEmail($mail,$options);
					        // Do not throw error if we're on a localhost as those are often not configured for email properly.
					        if (!$sent && ($modx->config['http_host'] != 'localhost')) {
					            return $modx->error->failure('Sending Email Failed for some reason...');
					        } else {
								/* We're done! Horray */
								return true;
							}
					    }
					}
				}
			}
		}
		
	} else {
		$hook->addError('email','The email "'.$email.'" already exists in our system. Did you perhaps already register?');
		return false;
	}
} else if ($modx->user->hasSessionContext($modx->context->get('key'))) {
	// Prevents Non-Logged-in users from using a user account simply because the have the correct email... Boy I was stupid!
	$uid = $modx->user->id;
	$hook->setValue('custId',$uid);
	return true;
	 
} else {
	$hook->setValue('custId',(int)0); 
	return true;
}
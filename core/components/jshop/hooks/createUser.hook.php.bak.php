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
$email = $hook->getValue('email');
$groupid = $modx->getOption('js.group_id',$scriptProperties,(int)1);

if($savecustomer == true) {
	$response = $modx->runProcessor('security/user/create',array(
	   'username' => $hook->getValue('email'),
	    'fullname' => $hook->getValue('fullname'),
	    'address' => $hook->getValue('address'),
	    'city' => $hook->getValue('city'),
		'state' => $hook->getValue('state'),
	    'zip' => $hook->getValue('zip'),
		'phone' => $hook->getValue('phone'),
	    'email' => $hook->getValue('email'),
		'active' => TRUE,
	    'newpassword' => TRUE,
	    'passwordnotifymethod' => 'e',
	    'passwordgenmethod' => 'g',
	));
	if ($response->isError()) {
	   if ($response->hasFieldErrors()) {
	       $fieldErrors = $response->getAllErrors();
	       $errorMessage = implode("\n",$fieldErrors);
	   } else {
	       $errorMessage = 'An error occurred: '.$response->getMessage();
	   }
	   $hook->addError('error_message',$errorMessage);
	return false;
	}
	$user = $modx->getObject('modUser',array('username' => $email));
	if ($user) {
		$internalkey = $user->get('id');
		$profile = $user->getOne('Profile',array('internalKey'=>$internalkey));
		$user->joinGroup((int)$groupid);
		if(!$user->save()){
			$hook->addError('error_message','Could Not Join User Group');
			return false;
		} else { $hook->setValue('custId',$user->get('id')); return true; }
	} else { $hook->addError('error_message','Could not get user object'); return false; }
} else { $hook->setValue('custId',(int)0); return true; }
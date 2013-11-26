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
// status of a logged in user or not
$loggedin = $modx->user->isAuthenticated($modx->context->get('key'));


// when logged in
if($loggedin) {
  $o = "";
  $user = $modx->user;
  $profile = $user->getOne('Profile');
  $extended = $profile->get('extended');
  $fieldsJs = array('fullname','email','address','city','state','zip','phone');
  
  foreach($profile->toArray() as $fieldname => $fieldvalue) {
    $hook->setValue($fieldname, $fieldvalue);
	if(in_array($fieldname,$fieldsJs)) {
		$o .= "_('#".$fieldname."').value('".$fieldvalue."');";
		$o .= "Shop.cart.form.".$fieldname." = '".$fieldvalue."';";
	}
  }
  
  foreach($extended as $fieldname => $fieldvalue) {
    $hook->setValue($fieldname, $fieldvalue);
	if(in_array($fieldname,$fieldsJs)) {
		$o .= "_('#".$fieldname."').value('".$fieldvalue."');";
		$o .= "Shop.cart.form.".$fieldname." = '".$fieldvalue."';";
	}
  }

  $o .= "
		if (Shop.cart.form.different_shipping['on'] == '0') {
			Shop.cart.region = '".$profile->get('state')."';
		}
		Shop.update();
	";

  $modx->regClientHTMLBlock('
	<script type="text/javascript">'. 
		$o
	.'</script>');
}

return true;
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
$cs = '';

$js = $modx->getService('jshop','jShop',$modx->getOption('js.core_path',null,$modx->getOption('core_path').'components/jshop/').'model/jshop/',$scriptProperties);
if (!($js instanceof jShop)) return '';

/* Setup Options */
$rowtpl = $modx->getOption('spRowTpl',$scriptProperties,'spRowTpl');
$outertpl = $modx->getOption('spOuterTpl',$scriptProperties,'spOuterTpl');

if($modx->user->hasSessionContext($modx->context->get('key'))) {
	$extended = $modx->user->Profile->get('extended');
	$cards = $extended['card'];
	if(!empty($cards) && count($cards) >= 1) {
		foreach($cards as $card) {
			$cs.= $js->getChunk($rowtpl,$card);
		}
		$output .= $js->getChunk($outertpl,array('wrapper'=>$cs));
	}
}

return $output;
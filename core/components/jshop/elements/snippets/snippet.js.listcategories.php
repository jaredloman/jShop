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
$rowtpl = $modx->getOption('rowtpl',$scriptProperties,'catlistrowtpl');
$parenttpl = $modx->getOption('parenttpl',$scriptProperties,'catlistparenttpl');
$outertpl = $modx->getOption('outertpl',$scriptProperties,'catlistoutertpl');
$childoutertpl = $modx->getOption('childoutertpl',$scriptProperties,'catlistchildoutertpl');
$sort = $modx->getOption('sort',$scriptProperties,'id');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');

$ca = array();
$cc = '';
$childArray = array();
$kids = '';

$c = $modx->newQuery('jsCategory');
$c->where(array('parent:<' => 1));
$c->sortby($sort,$dir);

$total = $modx->getCount('jsCategory',$c);
$cats = $modx->getCollection('jsCategory',$c);

if ($total < 1) return 'There are currently no categories.';

foreach($cats as $cat) {
	$cid = $cat->get('id');
	$d = $modx->newQuery('jsCategory');
	$d->where(array('parent' => $cid));
	$d->sortby($sort,$dir);
	
	$ctotal = $modx->getCount('jsCategory',$d);
	
	if ($ctotal > 0) {
		$children = $modx->getCollection('jsCategory',$d);
		foreach($children as $child) {
			$childArray = $child->toArray();
			$kids .= $js->getChunk($rowtpl,$childArray);
		}
		$childOuter = $js->getChunk($childoutertpl,array('wrapper' => $kids));
		unset($kids);
	}
	
	$ca = $cat->toArray();
	if (!empty($childOuter)) {
		$ca['children'] = $childOuter;
		unset($childOuter);
	}
	$cc .= $js->getChunk($parenttpl,$ca);
}
$output = $js->getChunk($outertpl,array('wrapper' => $cc));
return $output;
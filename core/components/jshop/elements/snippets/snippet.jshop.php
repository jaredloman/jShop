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
$itemlisttpl = $modx->getOption('itemlisttpl',$scriptProperties,'itemlisttpl');
$itemlistoutertpl = $modx->getOption('itemlistoutertpl',$scriptProperties,'itemlistoutertpl');
$itemdisplaytpl = $modx->getOption('itemdisplaytpl',$scriptProperties,'itemdisplaytpl');
$cattpl = $modx->getOption('cattpl',$scriptProperties,'cattpl');
$catoutertpl = $modx->getOption('catoutertpl',$scriptProperties,'catoutertpl');
$optiontpl = $modx->getOption('optiontpl',$scriptProperties,'optiontpl');
$optionvaltpl = $modx->getOption('optionvaltpl',$scriptProperties,'optionvaltpl');
$imagetpl = $modx->getOption('imagetpl',$scriptProperties,'imagetpl');
$outertpl = $modx->getOption('outertpl',$scriptProperties,'outertpl');
$catId = $modx->getOption('catId',$scriptProperties,'');
$showinactive = $modx->getOption('showinactive',$scriptProperties,0);
$sort = $modx->getOption('sort',$scriptProperties,'index');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$idx = !empty($idx) ? $idx : (integer)1;
$limit = $modx->getOption('limit',$scriptProperties,10);
$offset = $modx->getOption('offset',$scriptProperties,0);
$totalVar = $modx->getOption('totalVar', $scriptProperties, 'total');
if(isset($_REQUEST['c'])) $ctid = $_REQUEST['c'];
if(isset($_REQUEST['p'])) $pid = $_REQUEST['p'];

// Check for FURLS
$furls = $modx->getOption('friendly_urls',$scriptProperties,0);

if(!empty($pid)) { 
	$c = $modx->newQuery('jsItem');
	$graph = array('Image' => array(),'Category' => array());
	$c->where(array('alias' => $pid));
	if($showinactive == 0) $c->where(array('active' => 'yes'));
	$c->bindGraph($graph);
	$c->sortby($sort,$dir);
	//return print_r($c->toSQL($c->prepare()));
	$item = $modx->getObjectGraph('jsItem', $graph, $c);
	if(!$item) { return 'Product Not Found'; }
	
	$d = $modx->newQuery('jsOption');
	$d->sortby('jsOption_index',$dir);
	$d->where(array('prodId' => $item->get('id')));
	
	$options = $modx->getCollection('jsOption',$d);
	
	// Set Category Name
	if(!empty($item->Category)) $item->set('category',$item->Category->get('name'));

	//Process Images
	if(!empty($item->Image)) {
		$iidx = $idx;
		$img = '';
		foreach($item->Image as $image) {
			$ipath = $js->processImagePath($image->get('image'));
			$image->set('image',$ipath);
			$img .= $js->getChunk($imagetpl,array('image' => $image->get('image'), 'alt' => $image->get('description'), 'idx' => $iidx));
			$iidx++;
		}
		if($item->set('image',$img)) { unset($img); } else { return var_dump($img); }
	}
	//Setup Product Options
	if(!empty($options)) {
		$oidx = $idx;
		$opt = '';
		foreach($options as $option) {
			$vc = $modx->newQuery('jsOptionVals');
			$vc->where(array('optId' => $option->get('id')));
			$vc->sortby('jsOptionVals_index',$dir);
			$values = $modx->getCollection('jsOptionVals',$vc);
			if(!empty($values)) {
				$va = '';
				foreach($values as $value) {
					$v = $value->toArray();
					$va .= $js->getChunk($optionvaltpl,$v);
				}
				$option->set('vals',$va);
				unset($va);
			}
			$option->set('idx',$oidx);
			$oa = $option->toArray();
			$opt .= $js->getChunk($optiontpl,$oa);
			$oidx++;
		}
		$item->set('options',$opt);
		unset($opt);
	}
	// Finalize Item Template
	$item = $item->toArray();
	$output .= $js->getChunk($itemdisplaytpl,$item);
	return $output;
} elseif(!empty($ctid)) { 
	$c = $modx->newQuery('jsItem');
	$c->where(array('catId' => $ctid));
	if($showinactive == 0) $c->where(array('active' => 'yes'));
	$total = $modx->getCount('jsItem',$c);
	$modx->setPlaceholder($totalVar,$total);
	$c->limit($limit,$offset);
	$c->sortby('jsItem_id',$dir);
	//return print_r($c->toSQL($c->prepare()));
	$items = $modx->getCollection('jsItem',$c);
	if(!$items) return 'No items found in this category.';
	
	$category = $modx->getObject('jsCategory',$ctid);
	$citems = '';
	foreach($items as $item) {
		$image = $item->getOne('Image');
		if($image) {
			$item->set('image',$js->processImagePath($image->get('image')));
			$item->set('alt',$image->get('description'));
		}
		$item->set('category',$category->get('name'));
		$itemArray = $item->toArray();
		$citems .= $js->getChunk($itemlisttpl,$itemArray);
	}
	$output .= $js->getChunk($itemlistoutertpl,array('category' => $category->get('name'),'catdesc' => $category->get('description'),'wrapper' => $citems));
	return $output;
} else {
	$c = $modx->newQuery('jsCategory');
	$c->where(array('parent:<' => 1));
	//$c->sortby($sort,$dir);
	$total = $modx->getCount('jsCategory',$c);
	$modx->setPlaceholder($totalVar,$total);
	$c->limit($limit,$offset);
	$categories = $modx->getCollection('jsCategory',$c);
	foreach($categories as $category) {
		$categoryArray = $category->toArray();
		$categoryArray['image'] = $js->processImagePath($categoryArray['image']);
		$output .= $js->getChunk($cattpl,$categoryArray);
	}
	return $output;
}

if (empty($output)) return "<pre>" . print_r($itemArray) . "</pre>";

return $output;
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
* @subpackage processor
*/
class ItemGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'jsItem';
    public $languageTopics = array('jshop:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'js.item';
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->leftJoin('jsImage','Image','jsItem.id = prodId AND Image.index = 0');
		$c->select(array(
			'jsItem.*',
			'image' => 'Image.image'
		));
	    $query = $this->getProperty('query');
		$cid = $this->getProperty('catId');
	    if (!empty($query)) {
	        $c->where(array(
	            'title:LIKE' => '%'.$query.'%',
	            'OR:content:LIKE' => '%'.$query.'%',
	        ));
	    }
		if (!empty($cid)) {
			$c->where(array('catId' => $cid));
		}
	    return $c;
	}
	public function prepareRow(xPDOObject $object) {
        $ta =  $object->toArray('', false, true, true);
		if(empty($ta['image'])) {
			$ta['image'] = 'NO';
		}
        return $ta;
    }
}
return 'ItemGetListProcessor';
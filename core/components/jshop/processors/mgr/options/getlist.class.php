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
class OptionGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'jsOption';
    public $languageTopics = array('jshop:default');
    public $defaultSortField = 'index';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'js.option';
	public function getData() {
	    $data = array();
	    $limit = intval($this->getProperty('limit'));
	    $start = intval($this->getProperty('start'));

	    /* query for chunks */
	    $c = $this->modx->newQuery($this->classKey);
	    $c = $this->prepareQueryBeforeCount($c);
	    $data['total'] = $this->modx->getCount($this->classKey,$c);
	    $c = $this->prepareQueryAfterCount($c);

	    $sortClassKey = $this->getSortClassKey();
	    $sortKey = $this->modx->getSelectColumns($sortClassKey,$this->getProperty('sortAlias',$sortClassKey),'',array($this->getProperty('sort')));
	    if (empty($sortKey)) $sortKey = $this->getProperty('sort');
	    $c->sortby($sortKey,$this->getProperty('dir'));
	    if ($limit > 0) {
	        $c->limit($limit,$start);
	    }

	    //$data['results'] = $this->modx->getCollection($this->classKey,$c);
	    // modified this line from original modprocessor in my addon so I can use the getCollectionGraph
	    $data['results'] = $this->modx->getCollectionGraph($this->classKey, '{ "OptionValues":{} }',$c);
	    return $data;
	}
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		//$c->leftJoin('jsOptionVals','OptionValues');
		$pid = $this->getProperty('prodId');
		if (!empty($pid)) {
			$c->where(array('prodId' => $pid));
		}
	    return $c;
	}
	
	public function prepareRow(xPDOObject $object) {
		$ta = $object->toArray('', false, true, true);
		if (!empty($ta['OptionValues']) && is_array($ta['OptionValues'])) {
			$values = '';
			foreach($ta['OptionValues'] as $optval) {
				$values .= $optval['name'] . ', ';
			}
		}
		$ta['values'] = $values;
		unset($values);
		return $ta;
	}
}
return 'OptionGetListProcessor';
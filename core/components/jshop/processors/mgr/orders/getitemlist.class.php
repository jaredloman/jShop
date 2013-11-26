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
class OrderGetItemListProcessor extends modObjectGetProcessor {
    public $classKey = 'jsOrder';
    public $languageTopics = array('jshop:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'js.order';
	public function process() {
        $list = array();
        $count = 0;
        $itemList = $this->object->get('products');
        $itemArray = $this->modx->fromJSON($itemList);
 
        foreach ($itemArray as $item) {
            // this part really depends on how $img is stored. Array?
            $list[] = $item;
            $count++;
        }
        return $this->outputArray($list,$count);
    }
}
return 'OrderGetItemListProcessor';
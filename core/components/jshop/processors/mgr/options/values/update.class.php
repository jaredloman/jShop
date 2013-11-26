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
class OptionValsUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'jsOptionVals';
    public $languageTopics = array('jshop:default');
    public $objectType = 'js.optionvals';
 	public function beforeSet() {
		$price = $this->getProperty('price');
		if(!empty($price)) {
			$price = trim(str_replace('$','',$price));
		} else {
			$price = '+0.00';
		}
        $this->setProperty('price',$price);

		$title = $this->getProperty('title');
		$alias = $this->getProperty('alias');
		if(strlen($alias) < 1 && !empty($title)) {
			$alias = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
		} else {
			$alias = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $alias), '-'));
		}
		$this->setProperty('alias',$alias);
        return parent::beforeSet();
    }
	/*public function beforeSave() {
		$price = $this->getProperty('price');
		if(substr($price,-3,1) != '.') {
			$this->addFieldError('price','Please properly format your price.');
		}
	}*/
}
return 'OptionValsUpdateProcessor';
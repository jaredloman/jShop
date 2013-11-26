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
* @subpackage controller
*/
class jShopCategoryManagerController extends jShopManagerController {
    public function process(array $scriptProperties = array()) {
 
    }
    public function getPageTitle() { return $this->modx->lexicon('jshop'); }
    public function loadCustomCssJs() {
		//$this->addJavascript($this->js->config['jsUrl'].'mgr/widgets/item/images.grid.js');
		$this->addJavascript($this->js->config['jsUrl'].'mgr/widgets/category/subcategories.grid.js');
		$this->addJavascript($this->js->config['jsUrl'].'mgr/widgets/category/category.panel.js');
        $this->addLastJavascript($this->js->config['jsUrl'].'mgr/sections/category.js');
    }
    public function getTemplateFile() { return $this->js->config['templatesPath'].'category.tpl'; }

}
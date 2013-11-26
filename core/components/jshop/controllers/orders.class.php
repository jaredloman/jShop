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
class jShopOrdersManagerController extends jShopManagerController {
    public function process(array $scriptProperties = array()) {
 
    }
    public function getPageTitle() { return $this->modx->lexicon('js.orders'); }
    public function loadCustomCssJs() {
        $this->addJavascript($this->js->config['jsUrl'].'mgr/widgets/customers/customer.update.window.js');
        $this->addJavascript($this->js->config['jsUrl'].'mgr/widgets/orders/items.grid.js');
        $this->addJavascript($this->js->config['jsUrl'].'mgr/widgets/orders/orders.grid.js');
        $this->addJavascript($this->js->config['jsUrl'].'mgr/widgets/orders/orders.panel.js');
        $this->addLastJavascript($this->js->config['jsUrl'].'mgr/sections/orders.js');
    }
    public function getTemplateFile() { return $this->js->config['templatesPath'].'orders.tpl'; }

}
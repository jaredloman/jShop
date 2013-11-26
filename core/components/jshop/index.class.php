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
require_once dirname(__FILE__) . '/model/jshop/jshop.class.php';
abstract class jShopManagerController extends modExtraManagerController {
    /** @var jShop $js */
    public $js;
    public function initialize() {
        $this->js = new jShop($this->modx);
 
        $this->addCss($this->js->config['cssUrl'].'mgr.css');
        $this->addJavascript($this->js->config['jsUrl'].'mgr/jshop.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            jShop.config = '.$this->modx->toJSON($this->js->config).';
        });
        </script>');

		/** Loads Custom JS for Item Edit page */

		if ($_GET['action'] == 'item') {
			if (is_numeric($_GET['id']) && $_GET['id'] > 0) {
				$item = $this->modx->getObject('jsItem',$_GET['id']);
				if (!$item) return $this->modx->error->failure('My obj not found!');
				$this->addHtml('<script type="text/javascript">
		        Ext.onReady(function() {
		            jShop.record = '.$this->modx->toJSON($item->toArray()).';
		        });
		        </script>');
			}
		}
		
		/** Loads Custom JS for Category Edit page */

		if ($_GET['action'] == 'category') {
			if (is_numeric($_GET['id']) && $_GET['id'] > 0) {
				$category = $this->modx->getObject('jsCategory',$_GET['id']);
				if (!$category) return $this->modx->error->failure('My obj not found!');
				$this->addHtml('<script type="text/javascript">
		        Ext.onReady(function() {
		            jShop.record = '.$this->modx->toJSON($category->toArray()).';
		        });
		        </script>');
			}
		}
		
		/** Loads Custom JS for Options Edit page */

		if ($_GET['action'] == 'options') {
			if (is_numeric($_GET['id']) && $_GET['id'] > 0) {
				$option = $this->modx->getObject('jsOption',$_GET['id']);
				if (!$option) return $this->modx->error->failure('My obj not found!');
				$this->addHtml('<script type="text/javascript">
		        Ext.onReady(function() {
		            jShop.record = '.$this->modx->toJSON($option->toArray()).';
		        });
		        </script>');
			}
		}

        return parent::initialize();
    }
    public function getLanguageTopics() {
        return array('jshop:default');
    }
    public function checkPermissions() { return true;}
}
class IndexManagerController extends jShopManagerController {
    public static function getDefaultController() { return 'orders'; }
}
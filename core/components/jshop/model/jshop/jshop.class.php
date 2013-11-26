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
* @subpackage snippet
*/
class jShop {
    public $modx;
    public $config = array();
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
 
        $basePath = $this->modx->getOption('js.core_path',$config,$this->modx->getOption('core_path').'components/jshop/');
        $assetsUrl = $this->modx->getOption('js.assets_url',$config,$this->modx->getOption('core_path').'components/jshop/');
        $assetsPath = $this->modx->getOption('js.assets_path',$config,$this->modx->getOption('core_path').'components/jshop/');
		
		$actionVar = $this->modx->getOption('actionVar',$config,'action');
		// Get Media Source ID
		$mediasourceId = $this->modx->getOption('js.mediasource_id',$config,'1');
			//Load MediaSource Class
			$this->modx->loadClass('sources.modFileMediaSource');
			$this->modx->loadClass('sources.modMediaSource');
			//Get Media Source Path from ID
			$msource = $this->modx->getObject('modFileMediaSource',$mediasourceId);
			$msource->initialize();
			$mpath = $msource->getBases('');
		$mediasourcePath = $mpath['url'];
		
        $this->config = array_merge(array(
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath.'model/',
			'modelsPath' => $basePath.'model/', // some stupid formit hook issue
            'processorsPath' => $basePath.'processors/',
            'templatesPath' => $basePath.'templates/',
            'chunksPath' => $basePath.'elements/chunks/',
            'jsUrl' => $assetsUrl.'js/',
            'cssUrl' => $assetsUrl.'css/',
            'assetsUrl' => $assetsUrl,
			'assetsPath' => $assetsPath,
            'connectorUrl' => $assetsUrl.'connector.php',
			'actionVar' => $actionVar,
			'mediasourceId' => $mediasourceId,
			'mediasourcePath' => $mediasourcePath,
        ),$config);
		$this->modx->addPackage('jshop',$this->config['modelsPath']);
    }

	public function processImagePath($image) {
		if (!empty($image)) {
			// Get Media Source ID
			$mediasourceId = $this->modx->getOption('js.mediasource_id',(integer)1,(integer)1);
			//Load MediaSource Class
			$this->modx->loadClass('sources.modFileMediaSource');
			$this->modx->loadClass('sources.modMediaSource');
			//Get Media Source Path from ID
			$msource = $this->modx->getObject('modFileMediaSource',$mediasourceId);
			$msource->initialize();
			$mpath = $msource->getBases('');
			$mediasourcePath = $mpath['url'];
			$image = $mediasourcePath . $image;
			return $image;
		} else { return '';}
	}
	
	public function getChunk($name,$properties = array()) {
	    $chunk = null;
	    if (!isset($this->chunks[$name])) {
	        $chunk = $this->_getTplChunk($name);
	        if (empty($chunk)) {
	            $chunk = $this->modx->getObject('modChunk',array('name' => $name));
	            if ($chunk == false) return false;
	        }
	        $this->chunks[$name] = $chunk->getContent();
	    } else {
	        $o = $this->chunks[$name];
	        $chunk = $this->modx->newObject('modChunk');
	        $chunk->setContent($o);
	    }
	    $chunk->setCacheable(false);
	    return $chunk->process($properties);
	}
	
	private function _getTplChunk($name,$postfix = '.chunk.tpl') {
	    $chunk = false;
	    $f = $this->config['chunksPath'].strtolower($name).$postfix;
	    if (file_exists($f)) {
	        $o = file_get_contents($f);
	        $chunk = $this->modx->newObject('modChunk');
	        $chunk->set('name',$name);
	        $chunk->setContent($o);
	    }
	    return $chunk;
	}
	
	public function emailStatus($oid,$status,$subj,$tpl) {
		$order = $this->modx->getObject('jsOrder',$oid);
		$status = $this->modx->getObject('jsStatus',intval($status));
		$addr = $order->get('email');
		$props = $order->toArray();
		$props['status'] = $status->get('name');
		$this->_sendEmail($addr,$subj,$props,$tpl);
	}

	private function _sendEmail($addr,$subj,$props,$tpl) {
		$emailsender = $this->modx->getOption('emailsender',$scriptProperties,NULL);
		$fromName = $this->modx->getOption('site_name',$scriptProperties,'Store');

		$message = $this->getChunk($tpl,$props);

		/* now load modMail, and setup options */
		$this->modx->getService('mail', 'mail.modPHPMailer');
		$this->modx->mail->set(modMail::MAIL_BODY,$message);
		$this->modx->mail->set(modMail::MAIL_FROM,$emailsender);
		$this->modx->mail->set(modMail::MAIL_FROM_NAME,$fromName);
		$this->modx->mail->set(modMail::MAIL_SENDER,$emailsender);
		$this->modx->mail->set(modMail::MAIL_SUBJECT,$subj);
		$this->modx->mail->address('reply-to',$emailsender);
		$this->modx->mail->setHTML(true);

		/* specify the recipient */
		$this->modx->mail->address('to',$addr);

		/* send! */
		if (!$this->modx->mail->send()) {
			$this->modx->log(xPDO::LOG_LEVEL_ERROR,'emailCustomer: Could not send email receipt to address: '.$addr.'. The error was: '.$this->modx->mail->mailer->ErrorInfo);
		}
		$this->modx->mail->reset();
	}
}
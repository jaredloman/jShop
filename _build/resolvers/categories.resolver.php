<?php
if (isset($object) && $object->xpdo) {
    $modx =& $object->xpdo;
}
if (!$modx) {
    require_once dirname(dirname(dirname(__FILE__))) . '/config.core.php';
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $modx= new modX();
    $modx->initialize('mgr');
    $modx->setLogLevel(modX::LOG_LEVEL_INFO);
    $modx->setLogTarget('ECHO');

    include_once MODX_CORE_PATH . 'xpdo/transport/xpdotransport.class.php';
    $options[xPDOTransport::PACKAGE_ACTION] = xPDOTransport::ACTION_INSTALL;
}

$modelPath = $modx->getOption('jshop.core_path',null,$modx->getOption('core_path').'components/jshop/').'model/';
$modx->addPackage('jshop',$modelPath);

$category = array (
	'id' => 1,
	'name' => 'Default',
	'description' => 'Default Category to simplify setup.',
	'image' => '',
	'parent' => '0',
	'index' => '0',
	'createdon' => NULL,
	'createdby' => '0',
	'editedon' => NULL,
	'editedby' => '0',
);

$obj = $modx->getObject('jsCategory',array('id' => (int)1));
if (!$obj) {
	$obj = $modx->newObject('jsCategory');
	$obj->fromArray($category);
	$obj->save();
}
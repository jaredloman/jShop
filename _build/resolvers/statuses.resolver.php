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

$statuses = array(
    array (
        'name' => 'Created',
        'removeable' => false,
		'default' => true,
    ),
	array (
	    'name' => 'Authorized',
	    'removeable' => false,
		'default' => false,
	),
	array (
	    'name' => 'Paid',
	    'removeable' => false,
		'default' => false,
	),
	array (
	    'name' => 'Shipped',
	    'removeable' => false,
		'default' => false,
	),
	array (
	    'name' => 'Canceled',
	    'removeable' => false,
		'default' => false,
	),
	array (
	    'name' => 'Refunded',
	    'removeable' => false,
		'default' => false,
	),
	array (
	    'name' => 'Complete',
	    'removeable' => false,
		'default' => false,
	),
	array (
	    'name' => 'Failed',
	    'removeable' => false,
		'default' => false,
	),
);

foreach ($statuses as $status) {
    /* @var fdmRoomStatus $obj */
    $obj = $modx->getObject('jsStatus',array('name' => $status['name']));
    if (!$obj) {
        $obj = $modx->newObject('jsStatus');
        $obj->fromArray($status);
        $obj->save();
    }
}
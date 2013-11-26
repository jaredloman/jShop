<?php
$modx = null;
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

$manager = $modx->getManager();
$objects = array(
    'jsItem','jsCategory','jsOption','jsOptionVals','jsImage','jsOrder','jsStatus'
);

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_INSTALL:
        foreach ($objects as $obj) {
            $manager->createObjectContainer($obj);
        }
    break;
}

return true;
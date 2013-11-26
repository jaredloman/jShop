<?php
/* @var modX $modx */
/* Main index Action */
$action = $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 0,
    'namespace' => 'jshop',
    'parent' => '0',
    'controller' => 'index',
    'haslayout' => '1',
    'assets' => '',
),'',true,true);


$menu = array();
/* Create the menu object to the index */
$menu[0] = $modx->newObject('modMenu');
$menu[0]->fromArray(array(
    'text' => 'jShop',
    'parent' => 'components',
    'description' => 'js.desc',
    'menuindex' => '2',
    'params' => '',
    'handler' => '',
),'',true,true);
$menu[0]->set('text','jShop');
$menu[0]->addOne($action);


$menu[1] = $modx->newObject('modMenu');
$menu[1]->fromArray(array(
    'text' => 'js.orders_management',
    'description' => 'js.orders_desc',
    'menuindex' => '4',
    'params' => '&action=orders',
    'parent' => 'jshop',
));
$menu[1]->set('text','js.orders_management');
$menu[1]->addOne($action);


$menu[2] = $modx->newObject('modMenu');
$menu[2]->fromArray(array(
    'text' => 'js.inventory_management',
    'description' => 'js.inventory_desc',
    'menuindex' => '3',
    'params' => '&action=inventory',
    'parent' => 'jshop',
));
$menu[2]->set('text','js.inventory_management');
$menu[2]->addOne($action);

foreach ($menu as $m) {
    $vehicle = $builder->createVehicle($m,array (
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'text',
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
            'Action' => array (
                xPDOTransport::PRESERVE_KEYS => true,
                xPDOTransport::UPDATE_OBJECT => true,
                xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
                xPDOTRANSPORT::RELATED_OBJECTS => false,
            ),
        ),
    ));
    $builder->putVehicle($vehicle);
    unset ($vehicle,$childActions,$action,$menu);
}
?>
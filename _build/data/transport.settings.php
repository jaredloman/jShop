<?php

$settings = array();
$settings['js.mediasource_id']= $modx->newObject('modSystemSetting');
$settings['js.mediasource_id']->fromArray(array(
    'key' => 'js.mediasource_id',
    'title' => 'Media Source ID',
    'description' => 'Enter the ID of the media source used for item images.',
    'value' => '1',
    'xtype' => 'textfield',
    'namespace' => 'jshop',
    'area' => 'MediaSources',
),'',true,true);
$settings['js.live_publishable']= $modx->newObject('modSystemSetting');
$settings['js.live_publishable']->fromArray(array(
    'key' => 'js.live_publishable',
    'title' => 'Publishable Key (Live)',
    'description' => 'Set your Stripe Live Publishable Key here',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'jshop',
    'area' => 'Stripe',
),'',true,true);
$settings['js.live_secret']= $modx->newObject('modSystemSetting');
$settings['js.live_secret']->fromArray(array(
    'key' => 'js.live_secret',
    'title' => 'Secret Key (Live)',
    'description' => 'Set your Stripe Live Secret Key here',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'jshop',
    'area' => 'Stripe',
),'',true,true);
$settings['js.mode']= $modx->newObject('modSystemSetting');
$settings['js.mode']->fromArray(array(
    'key' => 'js.mode',
    'title' => 'Sandbox Mode',
    'description' => 'Use Stripe in Sandbox mode?',
    'value' => '1',
    'xtype' => 'combo-boolean',
    'namespace' => 'jshop',
    'area' => 'Stripe',
),'',true,true);
$settings['js.test_publishable']= $modx->newObject('modSystemSetting');
$settings['js.test_publishable']->fromArray(array(
    'key' => 'js.test_publishable',
    'title' => 'Publishable Key (Test)',
    'description' => 'Set your Stripe Test Publishable Key here',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'jshop',
    'area' => 'Stripe',
),'',true,true);
$settings['js.test_secret']= $modx->newObject('modSystemSetting');
$settings['js.test_secret']->fromArray(array(
    'key' => 'js.test_secret',
    'title' => 'Secret Key (Test)',
    'description' => 'Set your Stripe Test Secret Key here',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'jshop',
    'area' => 'Stripe',
),'',true,true);
$settings['js.group_id']= $modx->newObject('modSystemSetting');
$settings['js.group_id']->fromArray(array(
    'key' => 'js.group_id',
    'title' => 'User Group',
    'description' => 'Choose the default user group for registrants',
    'value' => '',
    'xtype' => 'modx-combo-usergroup',
    'namespace' => 'jshop',
    'area' => 'Users',
),'',true,true);

return $settings;
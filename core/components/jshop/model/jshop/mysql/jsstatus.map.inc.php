<?php
$xpdo_meta_map['jsStatus']= array (
  'package' => 'jshop',
  'version' => NULL,
  'table' => 'js_status',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'removeable' => 0,
    'default' => 0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'removeable' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'default' => 
    array (
      'dbtype' => 'int',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Order' => 
    array (
      'class' => 'jsOrder',
      'local' => 'id',
      'foreign' => 'status',
      'cardinality' => 'many',
      'owner' => 'foreign',
    ),
  ),
);

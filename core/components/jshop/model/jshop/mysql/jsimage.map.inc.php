<?php
$xpdo_meta_map['jsImage']= array (
  'package' => 'jshop',
  'version' => NULL,
  'table' => 'js_images',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'image' => '',
    'description' => '',
    'prodId' => 1,
    'index' => 0,
  ),
  'fieldMeta' => 
  array (
    'image' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'prodId' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
    'index' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Item' => 
    array (
      'class' => 'jsItem',
      'local' => 'prodId',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

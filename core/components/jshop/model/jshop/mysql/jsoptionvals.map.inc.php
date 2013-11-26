<?php
$xpdo_meta_map['jsOptionVals']= array (
  'package' => 'jshop',
  'version' => NULL,
  'table' => 'js_option_vals',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'price' => NULL,
    'index' => 0,
    'optId' => 1,
    'stock' => 0,
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
    'price' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
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
    'optId' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
    'stock' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Option' => 
    array (
      'class' => 'jsOption',
      'local' => 'optId',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

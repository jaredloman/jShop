<?php
$xpdo_meta_map['jsOption']= array (
  'package' => 'jshop',
  'version' => NULL,
  'table' => 'js_options',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => '',
    'prodId' => 1,
    'index' => 0,
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
  'composites' => 
  array (
    'OptionValues' => 
    array (
      'class' => 'jsOptionVals',
      'local' => 'id',
      'foreign' => 'optId',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Product' => 
    array (
      'class' => 'jsItem',
      'local' => 'prodId',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

<?php
$properties = array(
    array(
        'name' => 'rowtpl',
        'desc' => 'prop_jshop.catlistrowtpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'catlistrowtpl',
        'lexicon' => 'jshop:properties',
    ),
	array(
	    'name' => 'parenttpl',
	    'desc' => 'prop_jshop.catlistparenttpl_desc',
	    'type' => 'textfield',
	    'options' => '',
	    'value' => 'catlistparenttpl',
	    'lexicon' => 'jshop:properties',
	),
	array(
        'name' => 'outertpl',
        'desc' => 'prop_jshop.catlistoutertpl_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'catlistoutertpl',
        'lexicon' => 'jshop:properties',
    ),
	array(
	    'name' => 'childoutertpl',
	    'desc' => 'prop_jshop.catlistchildoutertpl_desc',
	    'type' => 'textfield',
	    'options' => '',
	    'value' => 'catlistchildoutertpl',
	    'lexicon' => 'jshop:properties',
	),
    array(
        'name' => 'sort',
        'desc' => 'prop_jshop.sort_desc',
        'type' => 'textfield',
        'options' => '',
        'value' => 'id',
        'lexicon' => 'jshop:properties',
    ),
    array(
        'name' => 'dir',
        'desc' => 'prop_jshop.dir_desc',
        'type' => 'list',
        'options' => array(
            array('text' => 'prop_jshop.ascending','value' => 'ASC'),
            array('text' => 'prop_jshop.descending','value' => 'DESC'),
        ),
        'value' => 'ASC',
        'lexicon' => 'jshop:properties',
    ),
);
return $properties;
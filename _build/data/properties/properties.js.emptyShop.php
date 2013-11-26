<?php
$properties = array(
    array(
        'name' => 'forceSuccess',
        'desc' => 'prop_jshop.forceSuccess_desc',
        'type' => 'textfield',
        'options' => array(
            array('text' => 'prop_jshop.true','value' => '1'),
            array('text' => 'prop_jshop.false','value' => '0'),
        ),
        'value' => '0',
        'lexicon' => 'jshop:properties',
    ),
);
return $properties;
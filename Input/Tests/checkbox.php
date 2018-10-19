<?php

?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1 id="logo">
Checkbox Unit Tests
</h1>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

define( 'ABSPATH', '1');
function esc_attr( $attr ) { return $attr; }
function apply_filters( $name, $values) { return $values; }

include_once( '..\Factory.php' );

Wkwgs_Logger::clear();

$form = \Input\Factory::Get(
        array(
        'type'          => 'form',
        'name'          => 'checkbox_test_form',
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'checkbox',
        'name'          => 'name only'
        )
    )
);
$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'checkbox',
        'name'          => 'checkbox with label',
        'label'         => 'want a good time?',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'checkbox',
        'name'          => 'checkbox enabled',
        'label'         => 'this is the label',
        'value'         => 'yes',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'checkbox',
        'name'          => 'escape html',
        'label'         => '< html & special chars >',
        )
    )
);

$form->html_print();

?>

</body>
</html>

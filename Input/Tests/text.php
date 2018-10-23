<?php

?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1 id="logo">
Text Unit Tests
</h1>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

define( 'ABSPATH', '1');
function esc_attr( $attr ) { return $attr; }
function apply_filters( $name, $values) { return $values; }

session_start();
include_once( '..\Factory.php' );

//Wkwgs_Logger::clear();

$form = \Input\Factory::Get(
        array(
        'type'          => 'form',
        'name'          => 'text_test_form',
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'text',
        'name'              => 'text_field',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'email',
        'name'              => 'email',
        'label'             => 'email address',
        'requried'          => 'yes',
        'value'             => 'abc@xyz.com',
        )
    )
);

foreach ( [ '', 'bogus', 'top', 'bottom', 'left', 'right' ] as $text_pos )
{
    $name = empty( $text_pos ) ? 'empty' : $text_pos;

    $form->add_field(
        \Input\Factory::Get(
            array(
            'type'              => 'text',
            'name'              => $name,
            'label'             => $name,
            'text-position'     => $text_pos,
            'placeholder'       => 'placeholder',
            )
        )
    );
}

// -------------------------------------------------------------------------------
// Called if we should muck with the post data to test validation
function falsify_post( $post )
{
    $post[ 'email' ] = 'this-is-not-a-valid-email-address <&>';

    return $post;
}

include_once( 'test_common_submit.php' );

?>

</body>
</html>

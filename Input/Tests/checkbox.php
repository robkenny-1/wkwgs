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

session_start();
include_once( '..\Factory.php' );

//Wkwgs_Logger::clear();

$form = \Input\Factory::Get(
        array(
        'type'              => 'form',
        'name'              => 'checkbox_test_form',
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'checkbox',
        'name'              => 'name_only'
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'checkbox',
        'name'              => 'checkbox_with_label',
        'label'             => 'checkbox_with_label',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'checkbox',
        'name'              => 'checkbox_required',
        'label'             => 'Value Required',
        'required'          => 'True',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'checkbox',
        'name'              => 'checkbox_enabled',
        'label'             => 'checkbox_enabled',
        'checked'           => 'yes',
        'value'             => 'Has Been Checked',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'checkbox',
        'name'              => 'html_and_special_chars',
        'label'             => '< html & special chars >',
        'help'              => 'This checkbox contains special HTML chars like <, >, &',
        )
    )
);

// -------------------------------------------------------------------------------
// Called if we should muck with the post data to test validation
function falsify_post( $post )
{
    $post[ 'checkbox_enabled' ] = 'does not match';

    return $post;
}

include_once( 'test_common_submit.php' );

?>

</body>
</html>

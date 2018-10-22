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
        'text-position'     => 'left',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'checkbox',
        'name'              => 'checkbox_enabled',
        'label'             => 'checkbox_enabled',
        'selection-value'   => 'boo',
        'value'             => 'boo',
        'text-position'     => 'top',
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

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'button',
        'name'              => 'submit',
        'value'             => 'Submit',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'button',
        'name'          => 'reset',
        'value'         => 'Reset',
        )
    )
);

// -------------------------------------------------------------------------------

// Store results in the session
$post = $form->get_submit_data();
if ( isset( $post ) )
{
    $form_values = $form->get_values();

    $_SESSION['form_values'] = $form_values;

    // Post/Redirect/Get
    // Redirect to this page
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

$form->html_print();

// Print out any results stored in the session
if ( isset( $_SESSION['form_values'] ) )
{
    echo "<div>";
    echo "<h1>Values from last SUBMIT</h1>";
    foreach ( $_SESSION['form_values'] as $name => $value)
    {
        echo "$name = $value</br>";
    }
    echo "</div>";
}
?>

</body>
</html>

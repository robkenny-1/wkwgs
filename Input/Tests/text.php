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
        'type'          => 'text',
        'name'          => 'text_field',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'text',
        'name'          => 'required_text_field',
        'label'         => 'Required data',
        'required'      => 'yes',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'text',
        'name'          => 'text_field_label',
        'label'         => 'Enter text here!',
        )
    )
);

// -------------------------------------------------------------------------------

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'button',
        'name'          => 'submit',
        'value'         => 'All Done',
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

$form->html_print();

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

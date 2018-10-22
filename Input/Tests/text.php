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
        'type'              => 'text',
        'name'              => 'right_required',
        'label'             => 'right, required',
        'required'          => 'yes',
        'text-position'     => 'right',
        'placeholder'       => 'placeholder for right',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'text',
        'name'              => 'left',
        'label'             => 'left',
        'text-position'     => 'left',
        'placeholder'       => 'placeholder for left',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'text',
        'name'              => 'top',
        'label'             => 'Top',
        'text-position'     => 'top',
        'placeholder'       => 'placeholder for top',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'text',
        'name'              => 'bottom',
        'label'             => 'Bottom',
        'text-position'     => 'bottom',
        'help'              => 'This is the help text for bottom'
        )
    )
);

// -------------------------------------------------------------------------------

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'          => 'button',
        'name'          => 'submit',
        'value'         => 'Submit',
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

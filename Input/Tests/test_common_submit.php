<?php
/*
    Input Copyright (C) 2018 Rob Kenny

    Input Fields is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Input Fields is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// -------------------------------------------------------------------------------
/*
$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'label',
        'name'              => 'label_before_buttons',
        'label'             => "--------------------------",
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'checkbox',
        'name'              => 'validate_post',
        'label'             => 'Use mock POST data',
        )
    )
);

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
        'button-type'   => 'reset',
        )
    )
);
*/

// -------------------------------------------------------------------------------

// Post/Redirect/Get
// To allow this PHP to handle form submission
// we store the submit data in the session
// then redraw the page
// Our normal rendering checks for any post data in the session
// and uses it to display the test results

// get the POST/GET data
//$post = $form->get_submit_data();

if ( isset( $post ) )
{
    // Clear session values
    unset( $_SESSION['post'] );
    unset( $_SESSION['post_orig'] );
    unset( $_SESSION['form_values'] );
    unset( $_SESSION['form_errors'] );

    $_SESSION['post_orig'] = $post;

    // Override $_POST values with test data
    if ( isset( $post[ 'validate_post' ] ) )
    {
        $post = falsify_post( $post );
    }
    $_SESSION['post'] = $post;

    // Validate data and store results
    if ( ! $form->validate( $post ) )
    {
        $errors = $form->get_validate_errors();
        $_SESSION['form_errors'] = serialize( $errors );
    }

    // Extract data and store results
    $form_values = $form->get_form_values( $post );
    $_SESSION['form_values'] = $form_values;

    // Redirect
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Display the form and all input objects
$form->render();

/*
if ( isset( $_SESSION['post_orig'] ) )
{
    echo '<h2>$_POST (Original)</h2>';
    print_r( $_SESSION['post_orig'] );
}

if ( isset( $_SESSION['post'] ) )
{
    echo '<h2>$_POST (After mock data)</h2>';
    print_r( $_SESSION['post'] );
}
*/

// Print out any validation errors stored in the session 
if ( isset( $_SESSION['form_errors'] ) )
{
    echo '<div>';
    echo '<h1>Errors from last SUBMIT</h1>';

    $errors = unserialize( $_SESSION['form_errors'] );
    foreach ( $errors as $name => $error)
    {
        $error_html = htmlspecialchars( $error->get_error() );
        $name_html  = htmlspecialchars( $name );
        echo "<b>Error Object:</b> $name_html => $error_html</br>";
    }
    echo '</div>';
}

// Print out any results stored in the session
if ( isset( $_SESSION['form_values'] ) )
{
    echo '<div>';
    echo '<h1>Values from last SUBMIT</h1>';

    $errors = $_SESSION['form_values'];
    foreach ( $errors as $name => $value)
    {
        echo "$name = $value</br>";
    }
    echo '</div>';
}

?>
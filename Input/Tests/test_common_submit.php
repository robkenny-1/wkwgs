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
$button_name = 'submit';

$submit = new Input\Button( [
    'attributes'    => [
    'type'          => 'submit',
    'name'          => $button_name,
    'value'         => 'submit',
    'label'         => 'Submit',
    ],
]);

$mock = new Input\Button( [
    'attributes'    => [
    'type'          => 'submit',
    'name'          => $button_name,
    'value'         => 'mock',
    'label'         => 'Use mock POST data',
    ],
]);

$clear = new Input\Button( [
    'attributes'    => [
    'type'          => 'submit',
    'name'          => $button_name,
    'value'         => 'clear',
    'label'         => 'Clear Session',
    ],
]);

$form->add_child(
    new Input\Element( [
        'tag'           => 'span',
        'contents'      => [ 'Form Buttons', $submit, $mock, $clear ]
    ])
);

// -------------------------------------------------------------------------------

// Post/Redirect/Get
// To allow this PHP to handle form submission
// we store the submit data in the session
// then redraw the page
// Our normal rendering checks for any post data in the session
// and uses it to display the test results

// get the POST/GET data
$post = $form->get_submit_data();

if ( ! empty( $post ) )
{
    Wkwgs_Logger::log_msg( 'test_common_submit: Clear session values' );

    // Clear session values
    unset( $_SESSION['post'] );
    unset( $_SESSION['post_orig'] );
    unset( $_SESSION['form_values'] );
    unset( $_SESSION['form_errors'] );
}

if ( isset( $post[ 'mock' ] ) )
{
    Wkwgs_Logger::log_msg( 'test_common_submit: Mock data' );

    $_SESSION['post_orig'] = $post;
    $post = falsify_post( $post );
}

if ( isset( $post[ 'submit' ] ) && $post[ 'submit' ] !== 'clear')
{
    Wkwgs_Logger::log_msg( 'test_common_submit: submit button' );

    $_SESSION['post'] = $post;

    // Validate data and store results
    $errors = $form->validate( $post );
    if ( ! empty( $errors ) )
    {
        $_SESSION['form_errors'] = serialize( $errors );
    }

    // Extract data and store results
    $form_values = $form->get_value( $post );
    $_SESSION['form_values'] = $form_values;

    // Redirect
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Display the form and all input objects
$form->render();

if ( isset( $_SESSION['post_orig'] ) )
{
    echo '<h2>$_POST (Original)</h2>';
    print_r( $_SESSION['post_orig'] );
}

if ( isset( $_SESSION['post'] ) )
{
    echo '<h2>$_POST</h2>';
    print_r( $_SESSION['post'] );
}

// Print out any validation errors stored in the session 
if ( isset( $_SESSION['form_errors'] ) )
{
    Wkwgs_Logger::log_msg( 'test_common_submit: $_SESSION["form_errors"] is set' );

    echo '<div>';
    echo '<h1>Validation Errors</h1>';

    $errors = unserialize( $_SESSION['form_errors'] );
    foreach ( $errors as $error)
    {
        if ( $error instanceof \Input\IHtmlValidateError )
        {
            $error_html = htmlspecialchars( $error->get_error() );
            $name_html  = htmlspecialchars( $error->get_name() );
            echo "<b>Error Object:</b> $name_html => $error_html</br>";

            if ( $error->get_name() == '$name is empty')
            {
                print_r( $error->get_object() );
            }
        }
        else
        {
            echo '<p>Ignore error<br>';
            print_r( $error );
            echo '<br></p>';
        }
    }
    echo '</div>';
}

// Print out any results stored in the session
if ( isset( $_SESSION['form_values'] ) )
{
    Wkwgs_Logger::log_msg( 'test_common_submit: $_SESSION["form_values"] is set' );

    echo '<div>';
    echo '<h1>Submit Values</h1>';

    $values = $_SESSION['form_values'];
    foreach ( $values as $name => $value)
    {
        echo "$name = $value<br>";
    }
    echo '</div>';
}

?>
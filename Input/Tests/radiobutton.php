<?php

?>
<!DOCTYPE html>
<html>
<head>

</head>
<body>
<h1 id="logo">
RadioButton Unit Tests
</h1>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

define( 'ABSPATH', '1');

session_start();
include_once( '..\Input.php' );
include_once( 'TestFramework.php' );

Wkwgs_Logger::clear();

$form = new Input\Form( [] );

/* -------------------------------------------------------------------------------- */

$form->add_child(
    new Input\Element( [
        'tag'           => 'h3',
        'contents'      => 'no selected value',
    ])
);

$form->add_child(
    new Input\RadioButton( [
        'attributes'            => [
            'name'              => 'required_1',
            'label'             => 'Please select a value',
            'choices'           => array(
                'choice1'       => 'Choice 1',
                'choice2'       => 'Choice 2',
                'choice3'       => 'Choice 3',
            ),
            'selected'          => 'something bogus',
        ],
        'contents'              => [],
    ])
);

/* -------------------------------------------------------------------------------- */

$form->add_child(
    new Input\Element( [
        'tag'           => 'h3',
        'contents'      => 'Required, Choice 2 should be selected',
    ])
);

$form->add_child(
    new Input\RadioButton( [
        'attributes'            => [
            'name'              => 'required_2',
            'label'             => '3 choices, required',
            'choices'           => array(
                'choice1'       => 'Choice 1',
                'choice2'       => 'Choice 2',
                'choice3'       => 'Choice 3',
            ),
            'selected'          => 'choice2',
            'required'          => 'yes',
        ],
        'contents'              => [],
    ])
);

/* -------------------------------------------------------------------------------- */

$form->add_child(
    new Input\Element( [
        'tag'           => 'h3',
        'contents'      => 'No label, Choice 3 should be selected',
    ])
);

$form->add_child(
    new Input\RadioButton( [
        'attributes'            => [
            'name'              => 'required_3',
            'choices'           => array(
                'choice1'       => 'Choice 1',
                'choice2'       => 'Choice 2',
                'choice3'       => 'Choice 3',
            ),
            'selected'          => 'choice3',
        ],
        'contents'              => [],
    ])
);


// -------------------------------------------------------------------------------
// Called if we should muck with the post data to test validation
function falsify_post( $post )
{
    $post[ 'required_1' ] = 'does not match';
    $post[ 'required_2' ] = '';
    unset( $post[ 'required_3' ] );

    return $post;
}

$test = new \Input\Test\TestFramework('falsify_post');
$test->test_form($form);

?>

</body>
</html>

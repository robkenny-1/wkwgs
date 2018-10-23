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
        'type'              => 'radio',
        'name'              => 'name_only',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'radio',
        'name'              => 'required_1',
        'choices'           => array( 'choice1', 'choice2', 'choice3' ),
        'label'             => array( 'Choice 1', 'Choice 2', 'Choice 3' ),
        'value'             => 'something bogus',
        'required'          => 'yes',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'radio',
        'name'              => 'required_2',
        'choices'           => array( 'choice1', 'choice2', 'choice3' ),
        'label'             => array( 'Choice 1', 'Choice 2', 'Choice 3' ),
        'value'             => 'choice2',
        'required'          => 'yes',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'radio',
        'name'              => 'required_3',
        'choices'           => array( 'choice1', 'choice2', 'choice3' ),
        'label'             => array( 'Choice 1', 'Choice 2', 'Choice 3' ),
        'value'             => 'choice3',
        'required'          => 'yes',
        )
    )
);

$layouts    = [ '', 'bogus', 'horizontal', 'vertical' ];
$text_poss  = [ '', 'bogus', 'top', 'bottom', 'left', 'right' ];

foreach ( $layouts as $layout)
{
    foreach ( $text_poss as $text_pos )
    {
        $name = ($layout   === '' ? 'default' : $layout) .
                ', ' .
                ($text_pos === '' ? 'default' : $text_pos);
                    
        $form->add_field(
            \Input\Factory::Get(
                array(
                'type'              => 'radio',
                'name'              => $name,
                'choices'           => [ 'choice1', 'choice2', 'choice3' ],
                'label'             => [ "$name 1", "$name 2", "$name 3" ],
                'layout'            => $layout,
                'text-position'     => $text_pos,
                )
            )
        );
    }
}

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

// -------------------------------------------------------------------------------
// Called if we should muck with the post data to test validation
function falsify_post( $post )
{
    $post[ 'required_1' ] = 'does not match';
    $post[ 'required_2' ] = '';
    unset( $post[ 'required_3' ] );

    return $post;
}

include_once( 'test_common_submit.php' );

?>

</body>
</html>
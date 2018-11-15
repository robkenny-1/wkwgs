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
include_once( '..\Input.php' );

Wkwgs_Logger::clear();

$form = new Input\Form([
        'attributes'        => [
            'name'          => 'checkbox_test_form',
        ],
        'contents'          => [   ],
]);

$form->add_child(
    new Input\Checkbox([
        'attributes'        => [
            'name'          => 'name_only',
        ],
        'contents'          => [   ],
    ])
);

$form->add_child(
    new Input\Checkbox([
        'attributes'        => [
            'name'          => 'checkbox_with_label',
            'label'         => 'checkbox with label',
        ],
        'contents'          => [   ],
    ])
);

$form->add_child(
    new Input\Checkbox([
        'attributes'        => [
            'name'          => 'checkbox_required',
            'label'         => 'Value Required',
            'required'      => 'True',
        ],
        'contents'          => [   ],
    ])
);

$form->add_child(
    new Input\Checkbox([
        'attributes'        => [
            'name'          => 'checkbox_enabled',
            'label'         => 'checkbox_enabled',
            'checked'       => True,
            'value'         => 'Has Been Checked',
        ],
        'contents'          => [   ],
    ])
);

$form->add_child(
    new Input\Checkbox([
        'attributes'        => [
            'name'          => 'html_and_special_chars',
            'label'         => '< html & special chars >',
            'help'          => 'This checkbox contains special HTML chars like <, >, &',
        ],
        'contents'          => [   ],
    ])
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

<?php
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1>
Checkbox Unit Tests
</h1>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

define('ABSPATH', '1');

session_start();
include_once ('..\Input.php');
include_once ('TestFramework.php');

/* -------------------------------------------------------------------------------- */

$form = new \Wkwgs\Input\Form([]);

/* -------------------------------------------------------------------------------- */

$form->add_child(new \Wkwgs\Input\Checkbox([
    'attributes' => [
        'name' => 'name_only',
    ],
    'contents' => [],
]));

$form->add_child(new \Wkwgs\Input\Checkbox([
    'attributes' => [
        'name' => 'checkbox_with_label',
        'label-text' => 'checkbox with label',
    ],
    'contents' => [],
]));

$form->add_child(new \Wkwgs\Input\Checkbox([
    'attributes' => [
        'name' => 'checkbox_required',
        'label-text' => 'Value Required',
        'required' => 'True',
    ],
    'contents' => [],
]));

$form->add_child(new \Wkwgs\Input\Checkbox([
    'attributes' => [
        'name' => 'checkbox_enabled',
        'label-text' => 'checkbox_enabled',
        'checked' => TRUE,
        'value' => 'Has Been Checked',
    ],
    'contents' => [],
]));

$form->add_child(new \Wkwgs\Input\Checkbox([
    'attributes' => [
        'name' => 'html_and_special_chars',
        'label-text' => '< html & special chars >',
        'help' => 'This checkbox contains special HTML chars like <, >, &',
    ],
    'contents' => [],
]));

// -------------------------------------------------------------------------------
// Called if we should muck with the post data to test validation
function falsify_post($post)
{
    $post['checkbox_enabled'] = 'does not match';

    return $post;
}

$test = new \Wkwgs\Input\Test\TestFramework('falsify_post');
$test->test_form($form);

?>

</body>
</html>

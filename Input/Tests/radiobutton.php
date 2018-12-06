<?php
?>
<!DOCTYPE html>
<html>
<head>
</head>
<body>
<h1>
RadioButton Unit Tests
</h1>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

define('ABSPATH', '1');

session_start();
include_once ('..\Input.php');
include_once ('TestFramework.php');

// Wkwgs_Logger::clear();

/* -------------------------------------------------------------------------------- */

$form = new \Wkwgs\Input\Form([]);

$form->add_child(new \Wkwgs\Input\Checkbox([
    'attributes' => [
        'name' => 'checkbox_with_label',
        'label-text' => 'checkbox with label',
    ],
    'contents' => [],
]));
/* -------------------------------------------------------------------------------- */

$form->add_child(new \Wkwgs\Input\Element([
    'tag' => 'h3',
    'contents' => 'no selected value',
]));

$form->add_child(new \Wkwgs\Input\RadioButton([
    'attributes' => [
        'name' => 'required_1',
        'label-text' => 'Please select a value',
        'choices' => array(
            'choice1',
            'choice2',
            'choice3',
        ),
        'choice1-label-text' => 'Choice 1',
        'choice2-label-text' => 'Choice 2',
        'choice3-label-text' => 'Choice 3',
    ],
    'contents' => [],
]));

/* -------------------------------------------------------------------------------- */

$form->add_child(new \Wkwgs\Input\Element([
    'tag' => 'h3',
    'contents' => 'Required, Choice 2 should be selected',
]));

$form->add_child(new \Wkwgs\Input\RadioButton([
    'attributes' => [
        'name' => 'required_2',
        'label-text' => '3 choices, required',
        'choices' => array(
            'choice1',
            'choice2',
            'choice3',
        ),
        'choice1-label-text' => 'Choice 1',
        'choice2-label-text' => 'Choice 2',
        'choice2-checked' => True,
        'choice3-label-text' => 'Choice 3',
        'required' => 'yes',
    ],
    'contents' => [],
]));

/* -------------------------------------------------------------------------------- */

$form->add_child(new \Wkwgs\Input\Element([
    'tag' => 'h3',
    'contents' => 'No label, Choice 3 should be selected',
]));

$form->add_child(new \Wkwgs\Input\RadioButton([
    'attributes' => [
        'name' => 'required_3',
        'choices' => array(
            'choice1',
            'choice2',
            'choice3',
        ),
        'choice1-style' => 'background-color: orange;',
        'choice1-label-style' => 'background-color: lightsteelblue;',
        'choice1-label-text' => 'Choice 1',
        'choice2-label-text' => 'Choice 2',
        'choice3-label-text' => 'Choice 3',
        'choice3-checked' => 'True',
    ],
    'contents' => [],
]));

// -------------------------------------------------------------------------------
// Called if we should muck with the post data to test validation
function falsify_post($post)
{
    $post['required_1'] = 'does not match';
    $post['required_2'] = '';
    unset($post['required_3']);

    return $post;
}

$test = new \Wkwgs\Input\Test\TestFramework('falsify_post');
$test->test_form($form);

?>

</body>
</html>

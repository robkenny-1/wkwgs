<?php
?>
<!DOCTYPE html>
<html>
<head>
<style>

body {
  font  : 21px sans-serif;
  padding : 2em;
  margin  : 0;
  background : #eeeeee;
}
form {
  margin: 2em 0;
  background : #dddddd;
}
 form input {
   border: 2px solid #555555;
 }

h1 {
color : red;
}
h2 {
color : blue;
}
h3 {
color : green;
}

button { margin: 5px; }

/*-----Tool tip-----*/
.tooltip-special {
  position:relative;
  display:inline-block;
  padding:5px;
  background-color : steelblue;
}
[data-tooltip] {
  cursor:help;
  background-color : lightyellow;
}
[data-tooltip]:after{
  visibility:hidden;
  opacity:0;
  position:absolute;
  content:attr(data-tooltip);
  background-color:white;
  box-shadow: 0 0px 1px 0px rgba(0, 0, 0, .1);
  border-bottom: 1px solid rgba(0, 0, 0, .15);
  border-radius:3px;
  width:320px;
  padding:10px;
  text-align:justify;
  margin-top:10px;
  margin-left:-10px;
  left:100%;
  transition: all .2s linear;
}
[data-tooltip]:hover:after, [data-tooltip] + input {
  visibility:visible;
  opacity:1;
}

<!-- Style to put label text on right -->
.text-input-label-right {
  position: relative;
  background: orange;
  box-sizing: border-box;
  padding: 5px;
}

.text-input-label-right input {
  float: left;
  background: green;
}
.text-input-label-right label {
  margin-left: 5px;
}

</style>
</head>
<body>
<h1 id="logo">
Text Unit Tests
</h1>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

define('ABSPATH', '1');

session_start();
include_once ('..\Input.php');
include_once ('TestFramework.php');

$form = new \Wkwgs\Input\Form([]);

/*
 * Disable as an exception is now thrown when input is missing name
 * $form->add_child(new \Wkwgs\Input\Text([
 * 'attributes' => [
 * 'class' => 'color-steelblue',
 * 'label-text' => 'I do not have a name, therefore no input results'
 * ],
 * 'contents' => [],
 * ]));
 */
/*
 * ------------------------------------------------------------------------------------
 */

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => 'tooltip',
                'label-text' => 'input with tooltip',
                'label-data-tooltip' => 'This is a tooltip message',
                'container-class' => 'tooltip-special'
            ],
        ]));

/*
 * ------------------------------------------------------------------------------------
 */

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => 'text_on_right',
                'label-text' => "I'm on the right",
                'container-class' => 'text-input-label-right'
            ],
        ]));

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => 'max3',
                'label-text' => 'No more than 3 chars',
                'maxlength' => '3',
            ],
        ]));

/*
 * ------------------------------------------------------------------------------------
 */

$form->add_child(
    new \Wkwgs\Input\Element(
        [
            'tag' => 'h3',
            'contents' => [
                new \Wkwgs\Input\HtmlText('Email input')
            ]
        ]));

$form->add_child(
    new \Wkwgs\Input\Email(
        [
            'attributes' => [
                'name' => 'email',
                'label-text' => 'email address',
                'required' => 'yes',
                'value' => 'abc@xyz.com',
            ],
        ]));

/*
 * ------------------------------------------------------------------------------------
 */

$form->add_child(
    new \Wkwgs\Input\Element(
        [
            'tag' => 'h3',
            'contents' => [
                new \Wkwgs\Input\HtmlText('Telephone input')
            ],
        ]));

$form->add_child(
    new \Wkwgs\Input\Telephone(
        [
            'attributes' => [
                'name' => 'telephone',
                'label-text' => 'Phone number',
                'placeholder' => '999-555-1212',
            ],
        ]));

/*
 * ------------------------------------------------------------------------------------
 */

$form->add_child(
    new \Wkwgs\Input\Element(
        [
            'tag' => 'h3',
            'contents' => [
                new \Wkwgs\Input\HtmlText('Data Cleansing')
            ],
        ]));

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => 'default',
                'label-text' => 'default (trim)',
                'value' => '   aaa   ',
            ],
        ]));

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => 'trim',
                'label-text' => 'trim',
                'cleanse' => 'trim',
                'value' => '   trim   ',
            ],
        ]));

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => 'slashes',
                'label-text' => 'slashes',
                'cleanse' => 'slashes',
                'value' => '//=/=\=\\=/',
            ],
        ]));

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => 'html',
                'label-text' => 'html',
                'cleanse' => 'html',
                'value' => '<h1>',
            ],
        ]));

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => 'sql',
                'label-text' => 'sql',
                'cleanse' => 'sql',
                'value' => 'delete * from',
            ],
        ]));

/*
 * ------------------------------------------------------------------------------------
 */
$form->add_child(new \Wkwgs\Input\Element('br'));

$form->add_child(
    new \Wkwgs\Input\Text(
        [
            'attributes' => [
                'name' => '&gt;<angles>&lt;',
                'label-text' => '&gt;<angles>&lt;&#62;&#x3e;\u{003e}"',
                'value' => '&&amp;&gt;h1&lt;',
            ],
        ]));

$form->add_child(
    new \Wkwgs\Input\HtmlSnippet('This <em>word</em> should be emphasized'));

$form->add_child(
    new \Wkwgs\Input\Element(
        [
            'tag' => 'h3',
            'contents' => [
                new \Wkwgs\Input\HtmlText(
                    '<h1>HTML Chars (this should be an h3 [green] not an h1)')
            ]
        ]));

// -------------------------------------------------------------------------------
// Called if we should muck with the post data to test validation
function falsify_post($post)
{
    $post['email'] = 'this-is-not-a-valid-email-address <&>';
    $post['telephone'] = '123abc';
    $post['max3'] = '1234';

    return $post;
}

$test = new \Wkwgs\Input\Test\TestFramework('falsify_post');
$test->test_form($form);

?>

</body>
</html>

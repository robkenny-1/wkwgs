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

// Style to put label text on right
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
error_reporting(E_ALL|E_STRICT);

define( 'ABSPATH', '1');

session_start();
include_once( '..\Factory.php' );
include_once('..\HtmlHelper2.php');
include_once('..\Form.php');
include_once('..\Text.php');

use Input\HtmlHelper as hh;

Wkwgs_Logger::clear();

$form = new hh\Form( [] );

$text = new hh\Text( [
        'attributes'    => [
            'class' => 'color-steelblue',
            'label' => 'test1'
        ],
        'contents'      => [   ],
    ]);
$form->get_children()->add_child( $text );

/* ------------------------------------------------------------------------------------ */

$text = new hh\Text( [
        'attributes'    => [
            'name'          => 'tooltip',
            'label'         => 'input with tooltip',
            'data-tooltip'  => 'This is a tooltip message',
            'css-container' => 'tooltip-special'
        ],
        'contents'      => [
            'I have a tooltip!',
        ]
    ]);
$form->get_children()->add_child( $text );

/* ------------------------------------------------------------------------------------ */

$text = new hh\Text( [
        'attributes'    => [
        'name'          => 'tooltip',
        'label'         => "I'm on the right",
        'css-container' => 'text-input-label-right'
        ],
    ]);
$form->get_children()->add_child( $text );

/* ------------------------------------------------------------------------------------ */

$text = new hh\Text( [
        'attributes'    => [
        'name'              => 'email',
        'label'             => 'email address',
        'required'          => 'yes',
        'value'             => 'abc@xyz.com',
        ],
    ]);
$form->get_children()->add_child( $text );

// -------------------------------------------------------------------------------
// Called if we should muck with the post data to test validation
function falsify_post( $post )
{
    $post[ 'email' ] = 'this-is-not-a-valid-email-address <&>';

    return $post;
}

include_once( 'test_common_submit.php' );

?>

</body>
</html>

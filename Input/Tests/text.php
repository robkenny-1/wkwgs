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
include_once( '..\Input.php' );

//Wkwgs_Logger::clear();
Wkwgs_Logger::log_msg( 'text.php' );

$form = new Input\Form( [] );

$form->add_child(
    new Input\Text( [
        'attributes'    => [
            'class' => 'color-steelblue',
            'label' => 'I do not have a name, therefore no input results'
        ],
        'contents'      => [],
    ])
);

/* ------------------------------------------------------------------------------------ */

$form->add_child(
    new Input\Text( [
        'attributes'    => [
            'name'          => 'tooltip',
            'label'         => 'input with tooltip',
            'label-tooltip'  => 'This is a tooltip message',
            'css-container' => 'tooltip-special'
        ],
    ])
);

/* ------------------------------------------------------------------------------------ */

$form->add_child(
    new Input\Text( [
        'attributes'    => [
            'name'          => 'text_on_right',
            'label'         => "I'm on the right",
            'css-container' => 'text-input-label-right'
        ],
    ])
);

/* ------------------------------------------------------------------------------------ */

$form->add_child(
    new Input\Element( [
        'tag'           => 'h3',
        'contents'      => [ new Input\HtmlText('Email input') ]
    ])
);

$form->add_child(
    new Input\Text( [
        'attributes'    => [
            'type'              => 'email',
            'name'              => 'email',
            'label'             => 'email address',
            'required'          => 'yes',
            'value'             => 'abc@xyz.com',
        ],
    ])
);

/* ------------------------------------------------------------------------------------ */

$form->add_child(
    new Input\Element( [
        'tag'           => 'h3',
        'contents'      => [ new Input\HtmlText('Telephone input') ],
    ])
);

$form->add_child(
    new Input\Text( [
        'attributes'    => [
            'type'              => 'tel',
            'name'              => 'telephone',
            'label'             => 'Phone number',
            'placeholder'       => '999-555-1212',
        ],
    ])
);

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

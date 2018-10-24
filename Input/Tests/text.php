<?php

?>
<!DOCTYPE html>
<html>
<head>
<style>

body {
  font-family: Avenir Next, Avenir, SegoeUI, sans-serif;
}


form {
  margin: 2em 0;
}
/**
* Make the field a flex-container, reverse the order so label is on top.
*/
 
.field {
  display: flex;
  flex-flow: column-reverse;
  margin-bottom: 1em;
}
/**
* Add a transition to the label and input.
* I'm not even sure that touch-action: manipulation works on
* inputs, but hey, it's new and cool and could remove the 
* pesky delay.
*/
label.fancy, input.fancy {
  transition: all 0.2s;
  touch-action: manipulation;
}

input.fancy {
  font-size: 1.5em;
  border: 0;
  border-bottom: 1px solid #ccc;
  font-family: inherit;
  -webkit-appearance: none;
  border-radius: 0;
  padding: 0;
  cursor: text;
}

input.fancy:focus {
  outline: 0;
  border-bottom: 1px solid #666;
}

label.fancy {
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
/**
* Translate down and scale the label up to cover the placeholder,
* when following an input (with placeholder-shown support).
* Also make sure the label is only on one row, at max 2/3rds of the
* field—to make sure it scales properly and doesn't wrap.
*/
input.fancy:placeholder-shown + label.fancy {
  cursor: text;
  max-width: 66.66%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  transform-origin: left bottom;
  transform: translate(0, 2.125rem) scale(1.5);
}
/**
* By default, the placeholder should be transparent. Also, it should 
* inherit the transition.
*/
::-webkit-input-placeholder {
  opacity: 0;
  transition: inherit;
}
/**
* Show the placeholder when the input is focused.
*/
input.fancy:focus::-webkit-input-placeholder {
  opacity: 1;
}
/**
* When the element is focused, remove the label transform.
* Also, do this when the placeholder is _not_ shown, i.e. when 
* there's something in the input at all.
*/
input.fancy:not(:placeholder-shown) + label.fancy,
input.fancy:focus + label.fancy {
  transform: translate(0, 0) scale(1);
  cursor: pointer;
  }

</style>
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
include_once( '..\Factory.php' );

//Wkwgs_Logger::clear();

$form = \Input\Factory::Get(
        array(
        'type'          => 'form',
        'name'          => 'text_test_form',
    )
);


$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'text',
        'name'              => 'text_field',
        )
    )
);

$form->add_field(
    \Input\Factory::Get(
        array(
        'type'                  => 'text',
        'name'                  => 'Fancy_Text_Field',
        'label'                 => 'Fancy Text input via CSS',
        'placeholder'           => 'enter text here',
        'help'                  => 'You gotta type in stuff',
        'css-input-container'   => '',
        'css-input-span'        => '', 
        'css-label'             => 'fancy',
        'css-input'             => 'fancy',
        )
    )
);


$form->add_field(
    \Input\Factory::Get(
        array(
        'type'              => 'email',
        'name'              => 'email',
        'label'             => 'email address',
        'requried'          => 'yes',
        'value'             => 'abc@xyz.com',
        )
    )
);

foreach ( [ '', 'bogus', 'top', 'bottom', 'left', 'right' ] as $text_pos )
{
    $name = empty( $text_pos ) ? 'empty' : $text_pos;

    $form->add_field(
        \Input\Factory::Get(
            array(
            'type'              => 'text',
            'name'              => $name,
            'label'             => $name,
            'text-position'     => $text_pos,
            'placeholder'       => 'placeholder',
            )
        )
    );
}

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

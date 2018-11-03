<?php

?>
<!DOCTYPE html>
<html>
<head>
<style>

.color-steelblue {
  background-color : steelblue;
}

.color-red {
  background-color : red;
}

</style>
</head>
<body>
<h1 id="logo">
HtmlHelper basic Unit Tests
</h1>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

define( 'ABSPATH', '1');

session_start();
include_once( '..\Factory.php' );
include_once( '..\HtmlHelper2.php' );

use Input\HtmlHelper as hh;

\Wkwgs_Logger::clear();
$test = 0;

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element( [ 'tag' => 'h1' ] );
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element( [
        'tag'           => 'h1',
        'attributes'    => [ 'class' => 'color-steelblue' ],
        'contents'      => "should be steelblue"
    ]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element( [
        'tag'           => 'h2',
        'attributes'    => [ 'class' => 'color-red' ],
        'contents'      => "should be red"
    ]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element( [
        'tag'           => 'h2',
        'attributes'    => [ 'class' => 'color-red' ],
        'contents'      => "override default class, should be red"
    ]);
$e1->get_attributes()->set_attributes_default( [ 'class' => 'color-steelblue' ] );
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element( [
        'tag'           => 'h2',
        'contents'      => "using default class, should be steel-blue"
    ]);
$e1->get_attributes()->set_attributes_default( [ 'class' => 'color-steelblue' ] );
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e3 = new hh\Element([
    'tag'           => 'p',
    'contents'      => [
        new hh\HtmlText('line 1'),
        new hh\Element('br'),
        new hh\HtmlText('line 2'),
        new hh\Element('br'),
        new hh\HtmlText('line 3'),
    ]
]);
$e2 = new hh\Element([
    'tag'           => 'p',
    'contents'      => [ 'text before', $e3, 'text after' ]
]);
$e1 = new hh\Element([
    'tag'               => 'p',
    'contents'          => [ $e2 ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
\Wkwgs_Logger::log( '#### Callback ####' );

function callback_test1()
{
    return 'this is text from callback_test1';
}
class callback_test_class
{
    public static function callback_static()
    {
        return 'callback_static';
    }
    public function callback_noparam()
    {
        return 'callback_noparam';
    }
    public function callback_param( $param1, $param2 )
    {
        return "callback_param( '$param1', '$param2' )";
    }
}

$cb = new callback_test_class();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element([
    'tag'       => 'p',
    'contents'  => [
        new hh\Callback( 'callback_test1' ),
    ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element([
    'tag'       => 'p',
    'contents'  => [
        new hh\HtmlText('Calling static method on class'),
        new hh\Element( 'br' ),
        new hh\Callback( [ $cb, 'callback_static' ] ),
    ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element([
    'tag'       => 'p',
    'contents'  => [
        new hh\HtmlText('Calling static method on class'),
        new hh\Element( 'br' ),
        new hh\Callback( [ $cb, 'callback_noparam' ] ),
    ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new hh\Element([
    'tag'       => 'p',
    'contents'  => [
        new hh\HtmlText('Calling static method on class'),
        new hh\Element( 'br' ),
        new hh\Callback( [ $cb, 'callback_param' ], [  'param value 1', 'param value 2' ] ),
    ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test       = $test + 1;
$test_msg   = "#### Test $test, verify array_extract ####";
\Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$attributes = [
    'abc'   => 1,
    'def'   => 2,
    'ghi'   => 3,
];
$extract = [ 'def' ];

$extracted = hh\Helper::array_extract($attributes, $extract);

$e1 = new hh\Element([
    'tag'       => 'p',
    'contents'  => [
        new hh\HtmlText('Array Values'),
    ]
]);
foreach ( $attributes[0] as $e => $v)
{
    $e1->get_children()->add_child( new hh\Element('br') );
    $e1->get_children()->add_child( new hh\HtmlText( "$e => $v" ) );
}
$e1->render();

$e1 = new hh\Element([
    'tag'       => 'p',
    'contents'  => [
        new hh\HtmlText('Values Extracted'),
    ]
]);
foreach ( $extracted[0] as $e => $v)
{
    $e1->get_children()->add_child( new hh\Element('br') );
    $e1->get_children()->add_child( new hh\HtmlText( "$e => $v" ) );
}
$e1->render();

$e1 = new hh\Element([
    'tag'       => 'p',
    'contents'  => [
        new hh\HtmlText('Values Remaining'),
    ]
]);
foreach ( $extracted[1] as $e => $v)
{
    $e1->get_children()->add_child( new hh\Element('br') );
    $e1->get_children()->add_child( new hh\HtmlText( "$e => $v" ) );
}
$e1->render();

?>

</body>
</html>

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
error_reporting(E_ALL | E_STRICT);

define('ABSPATH', '1');

session_start();
include_once ('..\Input.php');

// \Wkwgs_Logger::clear();

$test = 0;

/* -------------------------------------------------------------------------------- */
$test = "h1 element";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'h1'
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test = "h1 with contents";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'h1',
    'attributes' => [
        'class' => 'color-steelblue'
    ],
    'contents' => "should be steelblue"
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test = "Red h2";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'h2',
    'attributes' => [
        'class' => 'color-red'
    ],
    'contents' => "should be red"
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test = "Default override 1";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'h2',
    'attributes' => [
        'class' => 'color-red'
    ],
    'contents' => "override default class, should be red"
]);
$existing_attr = $e1->get_attributes();
$e1->set_attributes($existing_attr, [
    'class' => 'color-steelblue'
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test = "default override 2";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'h2',
    'contents' => "using default class, should be steel-blue"
]);
$existing_attr = $e1->get_attributes();
$e1->set_attributes($existing_attr, [
    'class' => 'color-steelblue'
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test = "multiple children";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e3 = new \Wkwgs\Input\Element([
    'tag' => 'p',
    'contents' => [
        new \Wkwgs\Input\HtmlText('line 1'),
        new \Wkwgs\Input\Element('br'),
        new \Wkwgs\Input\HtmlText('line 2'),
        new \Wkwgs\Input\Element('br'),
        new \Wkwgs\Input\HtmlText('line 3'),
    ]
]);
$e2 = new \Wkwgs\Input\Element([
    'tag' => 'p',
    'contents' => [
        new \Wkwgs\Input\HtmlText('text before'),
        $e3,
        new \Wkwgs\Input\HtmlText('text after')
    ]
]);
$e1 = new \Wkwgs\Input\Element([
    'tag' => 'p',
    'contents' => [
        $e2
    ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
// \Wkwgs_Logger::log( '#### Callback ####' );
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

    public function callback_param($param1, $param2)
    {
        return "callback_param( '$param1', '$param2' )";
    }
}

$cb = new callback_test_class();

/* -------------------------------------------------------------------------------- */
$test = "callback function";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'p',
    'contents' => [
        new \Wkwgs\Input\Callback('callback_test1'),
    ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test = "callback static class";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'p',
    'contents' => [
        new \Wkwgs\Input\HtmlText('Calling static method on class'),
        new \Wkwgs\Input\Element('br'),
        new \Wkwgs\Input\Callback([
            $cb,
            'callback_static'
        ]),
    ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test = "callback ojbect method, no params";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'p',
    'contents' => [
        new \Wkwgs\Input\HtmlText('Calling static method on class'),
        new \Wkwgs\Input\Element('br'),
        new \Wkwgs\Input\Callback([
            $cb,
            'callback_noparam'
        ]),
    ]
]);
$e1->render();

/* -------------------------------------------------------------------------------- */
$test = "callback ojbect method, with params";
$test_msg = "#### Test $test ####";
// \Wkwgs_Logger::log( $test_msg );
echo "<h3>{$test_msg}</h3>";

$e1 = new \Wkwgs\Input\Element([
    'tag' => 'p',
    'contents' => [
        new \Wkwgs\Input\HtmlText('Calling static method on class'),
        new \Wkwgs\Input\Element('br'),
        new \Wkwgs\Input\Callback([
            $cb,
            'callback_param'
        ], [
            'param value 1',
            'param value 2'
        ]),
    ]
]);
$e1->render();

?>

</body>
</html>

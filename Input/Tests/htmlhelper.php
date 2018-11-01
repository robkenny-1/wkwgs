<?php

?>
<!DOCTYPE html>
<html>
<head>
<style>
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
include_once( '..\HtmlHelper2.php' );

\Wkwgs_Logger::clear();

\Wkwgs_Logger::log( '#### Test 01 ####' );
\HtmlHelper\HtmlHelper::render( null );

\Wkwgs_Logger::log( '#### Test 02 ####' );
\HtmlHelper\HtmlHelper::render( [] );

\Wkwgs_Logger::log( '#### Test 03 ####' );
\Input\HtmlHelper::render(
    new \HtmlHelper\Element( 'h1' )
);

/*
\Wkwgs_Logger::log( '#### Test 01 ####' );
\Input\HtmlHelper::render2( null );

\Wkwgs_Logger::log( '#### Test 02 ####' );
\Input\HtmlHelper::render2( [] );

\Wkwgs_Logger::log( '#### Test 03 ####' );
\Input\HtmlHelper::render2(
    [
        'element'               => 'h1',
    ]
);

\Wkwgs_Logger::log( '#### Test 04 ####' );
\Input\HtmlHelper::render2(
    [
        'element'               => 'h2',
        'attributes'            => [
            'class'             => 'type, attributes, no content',
        ],
    ]
);

\Wkwgs_Logger::log( '#### Test 05 ####' );
\Input\HtmlHelper::render2(
    [
        'element'               => 'h2',
        'contents'              => [ 'text' => 'type, contents, no attributes' ]
    ]
);

\Wkwgs_Logger::log( '#### Test 06 ####' );
\Input\HtmlHelper::render2(
    [
        'element'               => 'h3',
        'attributes'            => [
            'class'             => 'test with a paragraph child',
        ],
        'contents'              => [
            [
                'element'       => 'p',
                'contents'      => [[ 'text' => 'text in paragraph' ]]
            ],
        ],
    ]
);

function callback_test1()
{
    echo 'this is text from callback_test1';
}
class callback_test_class
{
    public static function callback_static()
    {
        echo 'callback_static';
    }
    public function callback_noparam()
    {
        echo 'callback_noparam';
    }
    public function callback_param( $param1, $param2 )
    {
        echo "callback_param( '$param1', '$param2' )";
    }
}

$cb = new callback_test_class();

\Wkwgs_Logger::log( '#### Test 07 ####' );
\Input\HtmlHelper::render2(
[
    [
        'element'               => 'p',
        'contents'              => 'callback tests',
    ],
    [
        'callback'              => 'callback_test1' // INVALID
    ],
    [
        'callback'              => [ $cb, 'callback_static' ],
        'params'                => [ 'param1', 'param2' ]
    ],
    [
        'element'               => 'p',
        'contents'          => [
            [
                'callback'      => [ $cb, 'callback_noparam' ],
            ]
        ],
    ],
    [
        'element'               => 'p',
        'contents'          => [
            [
                'callback'      => [ $cb, 'callback_param' ],
                'params'        => [ 'param1', 'param2' ]
            ]
        ],
    ],
]
);
*/


?>

</body>
</html>

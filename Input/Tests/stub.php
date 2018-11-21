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
Stub Tests
</h1>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

define( 'ABSPATH', '1');

include_once( '..\Input.php' );

Wkwgs_Logger::clear();
Wkwgs_Logger::$Disable = False;

function test()
{
    $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args() );

    $raw = '4257800901';
    $pattern = '^\+?(\(?[0-9]{3}\)?|[0-9]{3})[-\.\s]?[0-9]{3}[-\.\s]?[0-9]{4}$';
    $delim = '#';
    $logger->log_var( '$raw', $raw );
    $logger->log_var( '$pattern', $pattern );


    $regex = $delim . preg_quote( $pattern, $delim ) . $delim;
    $matches  = preg_match($regex, $raw);
    $logger->log_var( '$regex', $regex );
    $logger->log_var( '$matches', $matches );

    $regex = $delim . preg_replace( '([^\\])#', '$1\\#', $regex) . $delim;
    $matches  = preg_match($regex, $raw);
    $logger->log_var( '$regex', $regex );
    $logger->log_var( '$matches', $matches );

}


test();

?>

</body>
</html>

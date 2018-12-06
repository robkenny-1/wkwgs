<?php

/*
  Input Copyright (C) 2018 Rob Kenny

  WordPress Plugin Template is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  WordPress Plugin Template is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Contact Form to Database Extension.
  If not, see http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Wkwgs\Input;

// Exit if accessed directly
defined('ABSPATH') || exit;

/*
 * Constant values available to all Input classes
 */

/*
 * Default value to use for domain in __() function
 */
const DOMAIN = 'Input';

/*
 * Apply this prefix to all external names
 * HTML:       <li class="Input_el name field-size-large" data-label="Name">
 * JScript:    var Input_frontend = {"word_limit":"Word limit reached"};
 * PHP:        $Input_var = array( 'name' => 'value')
 */
// Use the same prefix for all languages, could be different for each language
const PREFIX_HTML    = DOMAIN . '_';
const PREFIX_JSCRIPT = DOMAIN . '_';
const PREFIX_PHP     = DOMAIN . '_';


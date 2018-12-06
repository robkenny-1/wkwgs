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

include_once('Input.php');

class Text extends InputElement
{
    const Tag_Type             = 'input';
    const Attributes_Default   = [
        'type' => 'text',
    ];
    const Attributes_Secondary = [
    ];

    public function __construct($desc)
    {
//$logger = new \Wkwgs_Function_Logger( __METHOD__, func_get_args() );
        
        if ( gettype( $desc ) !== 'array' )
        {
//$logger->log_var('$desc is not an array', $desc);
            return;
        }

        $desc['tag'] = self::Tag_Type;
        parent::__construct($desc);
    }

    /* ------------------------------------------------------------------------- */
    /* IAttributeProvider routines */
    /* ------------------------------------------------------------------------- */

    public function define_attribute_default(): array
    {
        $parent = parent::define_attribute_default();
        return array_merge($parent, self::Attributes_Default);
    }

    /* ------------------------------------------------------------------------- */
    /* IAttributeSecondaryProvider routines */
    /* ------------------------------------------------------------------------- */

    public function define_attribute_secondary(): array
    {
        $parent = parent::define_attribute_secondary();
        return array_merge($parent, self::Attributes_Secondary);
    }

    /* ------------------------------------------------------------------------- */
    /* InputElement routines */
    /* ------------------------------------------------------------------------- */

    /**
     * Validate data for text input
     *
     * @return array of HtmlValidateError, empty if no errors
     */
    public function validate_post(string $name, array $post): array
    {
//$logger = new \Wkwgs_Function_Logger( __METHOD__, func_get_args() );
        $ve = [];

        // Perform data validation
        // None

//$logger->log_return($ve);
        return $ve;
    }

    public function cleanse_data($raw)
    {
//$logger = new \Wkwgs_Function_Logger( __METHOD__, func_get_args() );

        $cleansed = null;

        // No cleansing necessary?
        $cleansed = $raw;

//$logger->log_return($cleansed);
        return $cleansed;
    }

}

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

namespace Input;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

include_once('Constants.php');

/**
 * The Form Class
 *
 * @since 1.0.0
 */
class Form
{

    /**
     * Form fields
     *
     * @var array
     */
    protected $form_fields = array();

    /**
     * Form settings
     *
     * @var array
     */
    protected $form_settings            = null;
    protected $form_default_settings    = null;

    /**
     * The Constructor
     *
     * @param int|WP_Post $form
     */
    public function __construct(  )
    {
    }

    /**
     * Get all form fields of this form
     *
     * @return array
     */
    public function get_fields()
    {
        return $this->form_fields;
    }

    /**
     * Add a form field to this form
     *
     * @return array
     */
    public function add_field( $field )
    {
        $this->form_fields[ $field->get_name()] = $field;
    }

    /**
     * Get default form settings
     *
     * @return array
     */
    public static function get_default_settings()
    {
        return array(
        );
    }

    /**
     * Set form settings
     *
     * @return array
     */
    public function set_settings( $form_settings )
    {
        $this->form_settings = $form_settings;
    }

    /**
     * Get form settings, applies default values
     *
     * @return array
     */
    public function get_settings()
    {
        $default  = $this->get_default_settings();

        if ( isnull( $this->form_settings ) )
        {
            return $default;
        }

        return array_merge( $default, $this->form_settings);
    }

    /*-----------------------------------------------------------------------*/
    /*
     * HTML additions
     */

}

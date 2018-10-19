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
include_once(__DIR__ . '/../Wkwgs_Logger.php' );

/**
 * The Field Class
 *
 * @since 1.0.0
 */
abstract class Field
{

    /*-------------------------------------------------------------------------*/
    /*                                                                         */
    /* All input classes must implement/override these                         */
    /*                                                                         */
    /*-------------------------------------------------------------------------*/

    const Input_Type = '';

    public function __construct( $name )
    {
        $defaults = $this->get_attributes_default();
        $defaults[ 'name' ] = $name;

        $this->set_attributes( $defaults );
    }

    /**
     * Attributes for all input fields
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        $default = array(
            'type'                  => self::Input_Type,
            'name'                  => '',
            'label'                 => '',
            'value'                 => '',
            'required'              => 'no',
            'id'                    => 0,
            'width'                 => 'large',
            'placeholder'           => '',
            'default'               => '',
            'size'                  => 40,
            'help'                  => '',
            'css-input'             => '',
            'css-label'             => '',
            'css-input-container'   => 'options_group',
            'css-input-row'         => 'form-row',
            'css-input-span'        => 'woocommerce-input-wrapper', 
        );

        return $default;
    }

    /**
     * Verify status of input data
     *
     * @return True if value meets criteria
     */
    abstract function validate( $post );

    /**
     * Extract object's value from post data
     *
     * @return input value
     */
    abstract function get_value( $post );

    /**
     * Render of the field in the frontend
     * This spits out the necessary HTML
     *
     * @return void
     */
    abstract function render( );

    /*-------------------------------------------------------------------------*/

    /**
     * Settings of the field
     *
     * @var string
     */
    private $attributes;

    /**
     * Get all the attributes of the field
     *
     * @return string, empty string if unset
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     * Merge $attributes
     *
     * @return string
     */
    public function set_attributes( $attributes )
    {
        \Wkwgs_Logger::log_function( 'set_attributes');
        \Wkwgs_Logger::log_var( '$attributes', $attributes );

        $attrs = is_null( $this->attributes ) ? array() : $this->attributes;

        $this->attributes = array_merge( $this->get_attributes_default(), $attrs, $attributes );
        \Wkwgs_Logger::log_var( '$this->attributes', $this->attributes );
    }

    /**
     * Get the value of a single attribute of the field
     *
     * @return string
     */
    public function get_attribute( $name )
    {
        $attr = '';
        if ( isset( $this->attributes[ $name ] ) )
        {
            $attr = $this->attributes[ $name ];
        }

        return $attr;
    }

    /*-------------------------------------------------------------------------*/
    /* Accessors for commonly used values */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the name of the field
     *
     * @return string
     */
    public function get_name()
    {
        return $this->attributes[ 'name' ];
    }

    /**
     * Check if a field is required
     *
     * @param  array  $attributes
     *
     * @return boolean
     */
    public function is_required(  )
    {
        return Field::is_true( $this-get_attribute('required') );
    }

    /**
     * Does the content of the string equate to a True value
     *
     * @return True if $str is a true value
     */
    public static function is_true( $str )
    {
        $str = strtolower( $str );

        return
            $str === 'yes'  ||
            $str === '1'    ||
            $str === 'true' ;
    }

    /*-------------------------------------------------------------------------*/

    /**
     * Identity of form containing the field
     *
     * @var string
     */
    private $form_id = '0';

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id( )
    {
        return $this->form_id;
    }

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id( $form_id )
    {
        $this->form_id = $form_id;
    }

    /*-------------------------------------------------------------------------*/
    /* HTML helper routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Apply prefix to HTML name to ensure uniqueness
     * 
     * @return string, formatted as Prefix_form_name
     */
    public function html_prefix( $name )
    {
        $form = $this->get_form_id();
        $form = empty( $form ) ? '' : '_' . $form;

        return PREFIX_HTML . $form . $name;
    }

    public function html_print( )
    {
        $name           = $this->html_prefix( $this->get_attribute( 'name' ) );
        $css_container  = $this->get_attribute( 'css-input-container' );
        $css_row        = $this->get_attribute( 'css-input-row' );

        ?>
        <div class="<?php echo $css_container ?>">
            <p class="<?php echo $css_row ?> " id="<?php echo $name . '_field' ?>" data-priority="">
                <?php $this->render() ?>
            </p>
            <?php
            if ( !empty( $attributes[ 'help' ] ) )
            {
                ?>
                <span class="<?php echo $this->html_prefix('help'); ?>"><?php echo stripslashes( $attributes['help'] ); ?></span>
                <?php
            }
            ?>
        </div>
        <?php
    }
}
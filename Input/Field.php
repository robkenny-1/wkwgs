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
include_once('Field_Error.php');
include_once('HtmlHelper.php');
include_once(__DIR__ . '/../Wkwgs_Logger.php' );

/**
 * The Field Class
 *
 * Layout of the output field
 *  <div class="css-container">
 *     <label class="css-label">
 *       Label Text
 *       <input class="css-input" />
 *     </label>
 *  </div>
 *
 * You don't need all three class definitions to style the elements
 * CSS Example:
 *
 * .checkbox {
 *   background-color: Blue;
 * }
 * .checkbox label {
 *   background-color: LightYellow;
 * }
 * 
 * .checkbox input {
 *   background-color: SeaGreen;
 * }
 * 
 */

abstract class Field
{
    /*-------------------------------------------------------------------------*/
    /*                                                                         */
    /* All input classes must implement/override these                         */
    /*                                                                         */
    /*-------------------------------------------------------------------------*/

    const Input_Type            = '';
    const Default_Attributes    = array(
            'type'                  => self::Input_Type,
            'name'                  => '',
            'id'                    => 0,
            'value'                 => '',
            'required'              => False,
            'width'                 => 'large',
            'placeholder'           => '',
            'size'                  => 40,
            'help'                  => '',
            'label'                 => '',
            'data-tooltip'          => '',
            'text-position'         => 'right',
            'hidden'                => False,
            'aria-hidden'           => False,
            'css-container'         => '',
            'css-input-help'        => '', 
            'css-label'             => '',
            'css-input'             => '',
        );

    public function __construct( $attributes )
    {
        $this->set_attributes( $attributes );
        $this->merge_attributes_default( self::Default_Attributes );
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

    /*
     * Current attributes of $this
     */
    private $attributes;

    /*
     * Default attributes of $this
     */
    private $attributes_default;

    /*
     * Attributes + defaults, used to improve perf of get_attribute
     */
    private $attributes_combined_cached;

    /**
     * Get all the attributes of the field
     *
     * @return string, empty string if unset
     */
    public function get_attributes()
    {
        // If no cached value, calculate now
        if ( is_null( $this->attributes_combined_cached ) )
        {
            $this->log_function( __FUNCTION__ );
            $combine_defaults = $this->get_attributes_default();
            $this->attributes_combined_cached = array_merge( $this->get_attributes_default(), $this->attributes );
            $this->log_var( 'combined attributes', $this->attributes_combined_cached );
        }

        return $this->attributes_combined_cached;
    }

    /**
     * Set the attributes for this input object,
     * this overrides any previous attributes
     *
     * @return nothing
     */
    public function set_attributes( $attributes )
    {
        $this->log_function( __FUNCTION__ );
        // Clear the cached value
        $this->attributes_combined_cached = null;

        $this->attributes = $attributes;
    }

    /**
     * Get the default values for this input object
     * it recursively calls and merges all the parent's defaults as well
     *
     * @return array|fully merged list of default values
     */
    public function get_attributes_default()
    {
        if ( get_parent_class( $this ) !== False )
        {
            return $this->attributes_default;
        }
        return array_merge( parent::get_attributes_default(), $this->attributes_default);
    }
    /**
     * Set the default attributes for this input object,
     * this overrides any previous defaults
     *
     * @return nothing
     */
    public function merge_attributes_default( $attributes )
    {
        // Clear the cached value
        $this->attributes_combined_cached = null;

        if ( is_null( $this->attributes_default ) )
        {
            $this->attributes_default = $attributes;
        }
        $this->attributes_default = array_merge( $this->attributes_default, $attributes );
    }

    /**
     * Get the value of a single attribute of the field
     *
     * @return string
     */
    public function get_attribute( $name )
    {
        $attributes = $this->get_attributes();

        $attr = '';
        if ( isset( $attributes[ $name ] ) )
        {
            $attr = $attributes[ $name ];
        }

        return $attr;
    }

    /**
     * Set the specified attribute
     *
     * @return nothing
     */
    public function set_attribute( $name, $value )
    {
        $this->log_function( __FUNCTION__ );
        // Clear the cached value
        $this->attributes_combined_cached = null;

        if ( is_null( $this->attributes ) )
        {
            $this->attributes = [];
        }
        $this->attributes[ $name ] = $value;
    }

    /*-------------------------------------------------------------------------*/
    /* Accessors for commonly used values */
    /*-------------------------------------------------------------------------*/

    /**
     * Get the name of the field
     *
     * @return string
     */
    public function get_type()
    {
        return $this->get_attributes( 'type' );
    }

    /**
     * Get the name of the field
     *
     * @return string
     */
    public function get_name()
    {
        return $this->get_attribute( 'name' );
    }

    /**
     * Check if a field is required
     *
     * @param  array  $attributes
     *
     * @return boolean
     */
    public function is_required()
    {
        $required = $this->get_attribute( 'required' );

        return HtmlHelper::is_true( $required );
    }

    /*-------------------------------------------------------------------------*/

    /**
     * Identity of form containing the field
     *
     * @var string
     */
    private $form_id = '';

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id()
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
    public function html_print( )
    {
        $this->log_function( __FUNCTION__ );
        $css_container  = $this->get_attribute( 'css-container' );

        ?>
        <div <?php HtmlHelper::print_attribute('class', $css_container) ?> >
            <?php $this->render() ?>
        </div>
        <?php
    }

    /**
     * Render the label and any children
     * does not require $this
     *
     * @return nothing
     */
    public function render_label_explicit(
        $input_callback,
        $input_callback_params,
        $label,
        $tooltip,
        $css,
        $required
        )
    {
        if ( $required )
        {
            $label .= '<abbr class="required" title="required">&nbsp;*</abbr>';
        }

        ?>
        <label
            <?php HtmlHelper::print_attribute( 'class',         $css ) ?>
            <?php HtmlHelper::print_attribute( 'data-tooltip',  $tooltip ) ?>
        >
        <?php
        echo $label;
        call_user_func_array( $input_callback, $input_callback_params );
        ?></label><?php             
    }

    /**
     * Generic routine to render the input's Label
     *
     * @return nothing
     */
    public function render_label(
        $input_callback,
        $input_callback_params
        )
    {
        $this->log_function( __FUNCTION__ );

        $label          = $this->get_attribute( 'label'        );
        $tooltip        = $this->get_attribute( 'data-tooltip' );
        $css_label      = $this->get_attribute( 'css-label'    );
        $required       = $this->is_required();

        $this->log_var( '$label'    , $label    );
        $this->log_var( '$tooltip'  , $tooltip  );
        $this->log_var( '$css_label', $css_label);
        $this->log_var( '$required ', $required );

        $this->render_label_explicit(
            $input_callback,
            $input_callback_params,
            $label,
            $tooltip,
            $css_label,
            $required
        );
    }

    /**
     * Render the attributes common to all input objects
     *
     */
    public function render_input_attributes( $exclude = null )
    {
        HtmlHelper::print_attribute( 'type'             , $this->get_attribute( 'type'          ),                  $exclude );
        HtmlHelper::print_attribute( 'name'             , $this->get_attribute( 'name'          ),                  $exclude );
        HtmlHelper::print_attribute( 'id'               , $this->get_attribute( 'id'            ),                  $exclude );
        HtmlHelper::print_attribute( 'value'            , $this->get_attribute( 'value'         ),                  $exclude );
        HtmlHelper::print_attribute( 'class'            , $this->get_attribute( 'css-input'     ),                  $exclude );
        HtmlHelper::print_attribute( 'required'         , $this->get_attribute( 'required'      ),                  $exclude );
        HtmlHelper::print_attribute( 'hidden'           , $this->get_attribute( 'hidden'        ),                  $exclude );
        HtmlHelper::print_attribute( 'aria-hidden'      , $this->get_attribute( 'aria-hidden'   ),                  $exclude );
        // move to <label>
        //HtmlHelper::print_attribute( 'width'            , $this->get_attribute( 'width'         ),              $exclude );
        HtmlHelper::print_attribute( 'placeholder'      , $this->get_attribute( 'placeholder'   ),                  $exclude );
        HtmlHelper::print_attribute( 'size'             , $this->get_attribute( 'size'          ),                  $exclude );
    }

    /*-------------------------------------------------------------------------*/
    /* Logging routines */
    /*-------------------------------------------------------------------------*/
    public function log_function( $func )
    {
        $msg = get_class( $this ) . "::$func";

        if ( isset( $this->attributes[ 'name' ] ) )
        {
            $msg .= ' (' . $this->attributes[ 'name' ] . ')';
        }
        else
        {
            $msg .= ' (name is unset)';
        }
        \Wkwgs_Logger::log_function( $msg );
    }
    public function log_var( $var_name, $var )
    {
        \Wkwgs_Logger::log_var( $var_name, $var );    
    }
    public function log_msg( $msg )
    {
        \Wkwgs_Logger::log_msg( $msg );    
    }
}

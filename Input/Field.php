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
include_once('HtmlHelper2.php');
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
 * .css-container {
 *   background-color: Blue;
 * }
 * .css-container label {
 *   background-color: LightYellow;
 * }
 * 
 * .css-container input {
 *   background-color: SeaGreen;
 * }
 * 
 */

abstract class Field implements \Input\HtmlHelper\IHtmlPrinter
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
            'container'             => 'div',
            'css-container'         => '',
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
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        // If no cached value, calculate now
        if ( is_null( $this->attributes_combined_cached ) )
        {
            $combine_defaults = $this->get_attributes_default();
            $this->attributes_combined_cached = array_merge( $this->get_attributes_default(), $this->attributes );
            $logger->log_var( 'combined attributes', $this->attributes_combined_cached );
        }

        $logger->log_return( $this->attributes_combined_cached );
        return $this->attributes_combined_cached;
    }

    /**
     * Set the attributes for this input object,
     * this overrides any previous attributes
     *
     * @return void
     */
    public function set_attributes( $attributes )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
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
     * @return void
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
     * @return void
     */
    public function set_attribute( $name, $value )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

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

        return \Input\HtmlHelper\HtmlHelper::is_true( $required );
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
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $css_container  = $this->get_attribute( 'css-container' );

        ?>
        <div <?php \Input\HtmlHelper\HtmlHelper::print_attribute('class', $css_container) ?> >
            <?php $this->render() ?>
        </div>
        <?php
    }

    /**
     * Render the label and any children
     * does not require $this
     *
     * @return void
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

        \Input\HtmlHelper\HtmlHelper::render_element(
            'label',
            [
                'class'         => $css,
                'data-tooltip'  => $tooltip,
            ],
            [
                'text'      => $label,
                'child'     =>
                [
                    'callback'  => $input_callback,
                    'params'    => $input_callback_params
                ],
            ]
        );
    }

    /**
     * Generic routine to render the input's Label
     *
     * @return void
     */
    public function render_label(
        $input_callback,
        $input_callback_params
        )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $label          = $this->get_attribute( 'label'        );
        $tooltip        = $this->get_attribute( 'data-tooltip' );
        $css_label      = $this->get_attribute( 'css-label'    );
        $required       = $this->is_required();

        $logger->log_var( '$label'    , $label    );
        $logger->log_var( '$tooltip'  , $tooltip  );
        $logger->log_var( '$css_label', $css_label);
        $logger->log_var( '$required ', $required );

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
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'type'             , $this->get_attribute( 'type'          ),                  $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'name'             , $this->get_attribute( 'name'          ),                  $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'id'               , $this->get_attribute( 'id'            ),                  $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'value'            , $this->get_attribute( 'value'         ),                  $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'class'            , $this->get_attribute( 'css-input'     ),                  $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'required'         , $this->get_attribute( 'required'      ),                  $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'hidden'           , $this->get_attribute( 'hidden'        ),                  $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'aria-hidden'      , $this->get_attribute( 'aria-hidden'   ),                  $exclude );
        // move to <label>
        //\Input\HtmlHelper\HtmlHelper::print_attribute( 'width'            , $this->get_attribute( 'width'         ),              $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'placeholder'      , $this->get_attribute( 'placeholder'   ),                  $exclude );
        \Input\HtmlHelper\HtmlHelper::print_attribute( 'size'             , $this->get_attribute( 'size'          ),                  $exclude );
    }

    public function render()
    {
        $html = $this->get_html();
        echo $html;
    }
}

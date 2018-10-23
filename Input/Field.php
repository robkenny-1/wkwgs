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

    public function __construct( $attributes )
    {
        $this->set_attributes( $attributes );
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
            'id'                    => 0,
            'value'                 => '',
            'required'              => 'no',
            'width'                 => 'large',
            'placeholder'           => '',
            'size'                  => 40,
            'help'                  => '',
            'label'                 => '',
            'text-position'         => 'right',
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
        //\Wkwgs_Logger::log_function( 'Field::set_attributes');
        //\Wkwgs_Logger::log_var( '$this->get_name()', $this->get_name() );
        //\Wkwgs_Logger::log_var( '$attributes', $attributes );
        if ( is_null( $this->attributes) )
        {
            $this->attributes = $this->get_attributes_default();
        }
        $this->attributes = array_merge( $this->attributes, $attributes );
        //\Wkwgs_Logger::log_var( '$this->attributes', $this->attributes );
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

    public function render_attributes( $except = null )
    {
        Field::html_print_attribute( 'type'         , $this->get_attribute( 'type'          ), $except );
        Field::html_print_attribute( 'name'         , $this->get_attribute( 'name'          ), $except );
        Field::html_print_attribute( 'id'           , $this->get_attribute( 'id'            ), $except );
        Field::html_print_attribute( 'value'        , $this->get_attribute( 'value'         ), $except );
        Field::html_print_attribute( 'required'     , Field::is_true( $this->get_attribute( 'required' )), $except );
        Field::html_print_attribute( 'width'        , $this->get_attribute( 'width'         ), $except );
        Field::html_print_attribute( 'placeholder'  , $this->get_attribute( 'placeholder'   ), $except );
        Field::html_print_attribute( 'size'         , $this->get_attribute( 'size'          ), $except );
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
        return $this->attributes[ 'type' ];
    }

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
        return Field::is_true( $this->get_attribute('required') );
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
    private $form_id = '';

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

    public static function html_print_attribute( $attr, $value, $except = null )
    {
        if ( gettype( $except) === 'array' && array_contains( $attr, $exclude ) )
        {
            return;
        }

        if ( gettype( $value ) === 'boolean' && $value)
        {
            echo $attr . PHP_EOL;
            return;
        }

        if ( ! empty( $value ) )
        {
            $value = esc_attr( $value );
            echo $attr . '="' . $value . '"' . PHP_EOL;
            return;
        }
    }
    public function html_print( )
    {
        $name           = esc_attr( $this->get_attribute( 'name' )                  );
        $help           = htmlspecialchars( $this->get_attribute( 'help' )          );
        $css_container  = esc_attr( $this->get_attribute( 'css-input-container' )   );
        $css_row        = esc_attr( $this->get_attribute( 'css-input-row' )         );

        ?>
        <div class="<?php echo $css_container ?>">
            <p class="<?php echo $css_row ?> " id="<?php echo $name . '_field' ?>" data-priority="">
                <?php $this->render() ?>
            </p>
            <?php
            if ( !empty( $help) )
            {
                ?>
                <span class="help"><?php echo $help ?></span>
                <?php
            }
            ?>
        </div>
        <?php
    }
}

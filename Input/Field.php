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

include_once(WP_PLUGIN_DIR . '/wkwgs/Wkwgs_Logger.php' );

/**
 * The Field Class
 *
 * @since 1.0.0
 */
abstract class Field
{
    /*-------------------------------------------------------------------------*/
    /*
     * Derived classes must implement these functions, set these values
     */

    /**
     * Default attributes of a field
     *
     * Child classes should define any specific values for their implementation
     *
     * @return array
     */
    abstract public function get_attributes_default_for_class();

    /**
     * Render of the field in the frontend
     * This spits out the necessary HTML
     *
     * @return void
     */
    abstract function render( );

    /**
     * Get the type of Field
     */
    abstract function get_type( );

    /*-------------------------------------------------------------------------*/
    /* CSS styles */
    /*-------------------------------------------------------------------------*/

    private static $css = array(
        
        // Used by all classes
        'input-container'           => 'options_group',             // applies to all input classes
        'input-row'                 => 'form-row',                  // applies to all input classes
        'input'                     => 'woocommerce-input-wrapper', 
        
        // Input specific
        'checkbox'                  => 'input-checkbox',
        'checkbox-label'            => 'checkbox',
    );
    public static function get_css( $name )
    {
        if ( isset( Field::$css[ $name ] ) )
        {
            return Field::$css[ $name ];
        }
        return '';
    }
    /*-------------------------------------------------------------------------*/

    /**
     * Type of the field
     *
     * @var string
     */
    private $form_id = '0';

    /**
     * Settings of the field
     *
     * @var string
     */
    private $attributes;

    public function __construct( $name )
    {
        $this->set_attributes( array(
                'name'  => $name,
                'type'  => $this->get_type(),
            )
        );
    }

    /**
     * Default attributes of a field
     *
     * Child classes should use this default setting and add any extra values
     *
     * @return array
     */
    public function get_attributes_default()
    {
        // Combine the implementation's attributes with the values common to all input types
        return array_merge(
            array(
                'template'    => '',
                'name'        => '',
                'label'       => '',
                'required'    => 'no',
                'id'          => 0,
                'placeholder' => '',
                'help'        => '',
                'value'       => '',
            ),
            $this->get_attributes_default_for_class()
        );
    }

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
        $attrs = is_null( $this->attributes ) ? array() : $this->attributes;
        
        // Don't let $attributes override 'type'
        $type  = array ( 'type' => $this->get_type() );

        $this->attributes = array_merge( $this->get_attributes_default(), $attrs, $attributes, $type );
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
    public static function is_required( $attributes )
    {
        if ( isset( $attributes['required'] ) && $attributes['required'] == 'yes' ) {
            return true;
        }

        return false;
    }
    
    /**
     * Get the value of the field
     *
     * @return string
     */
    public function get_value( $value )
    {
        return $this->attributes[ 'value' ];
    }
    
    /**
     * Set the value of the field
     *
     * @return null
     */
    public function set_value( $value )
    {
        $this->attributes[ 'value' ] = $value;
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

    public function print_html( )
    {
        $name           = $this->html_prefix( $this->get_attribute( 'name' ) );
        $css_container  = $this->get_css( 'input-container' );
        $css_row        = $this->get_css( 'input-row' );
        $css_input      = $this->get_css( 'input' );

        ?>
        <div class="<?php echo $css_container ?>">
            <p class="<?php echo $css_row ?> " id="<?php echo $name . '_field' ?>" data-priority="">
                <span class="<?php echo $css_input ?>">
                <?php $this->render() ?>
                </span>
            </p>
            <?php
            if ( !empty( $attributes['help'] ) )
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
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

static $Logger = null;
function Logger()
{
    static $Logger = null;
    if ( is_null( $Logger ) )
    {
        $Logger = new \Wkwgs_Logger();
    }

    return $Logger;
}

/**
 * The Field Class
 *
 * @since 1.0.0
 */
abstract class Field
{

    /*-------------------------------------------------------------------------*/

    /**
     * Render of the field in the frontend
     * This spits out the necessary HTML
     *
     * @return void
     */
    abstract function render( );

    /*-------------------------------------------------------------------------*/
    /* Attributes common to all input fields */
    /*-------------------------------------------------------------------------*/

    const Class_Attributes              = array(
        'type'                          => '',
        'name'                          => '',
        'label'                         => '',
        'value'                         => '',
        'required'                      => 'no',
        'id'                            => 0,
        'width'                         => 'large',
        'placeholder'                   => '',
        'default'                       => '',
        'size'                          => 40,
        'help'                          => '',
        'css-field'                     => '',
        'css-label'                     => '',
        'css-input-container'           => 'options_group',
        'css-input-row'                 => 'form-row',
        'css-input'                     => 'woocommerce-input-wrapper', 
    );

    /**
     * Attributes for all input fields
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        return self::Class_Attributes;
    }

    /*-------------------------------------------------------------------------*/

    /**
     * Settings of the field
     *
     * @var string
     */
    private $attributes;

    public function __construct( $name )
    {
        $defaults = $this->get_attributes_default();
        $defaults[ 'name' ] = $name;

        $this->set_attributes( $defaults );
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

        $this->attributes = array_merge( $this->get_attributes_default(), $attrs, $attributes );
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

    public function print_html( )
    {
        $name           = $this->html_prefix( $this->get_attribute( 'name' ) );
        $css_container  = $this->get_attribute( 'css-input-container' );
        $css_row        = $this->get_attribute( 'css-input-row' );
        $css_input      = $this->get_attribute( 'css-input' );

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
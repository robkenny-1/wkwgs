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
 * The Field Class
 *
 * @since 1.0.0
 */
abstract class Field
{
    /*-------------------------------------------------------------------------*/
    /*
     * Derived classes must implement these functions
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
    abstract function render( $form_id );

    /*-------------------------------------------------------------------------*/
    /*
     * CSS styles
     */
    protected $css = array(
        
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
        if ( isset( $css[ $name ] ) )
        {
            return $css[ $name ];
        }
        return '';
    }
    /*-------------------------------------------------------------------------*/

    /**
     * Type of the field
     *
     * @var string
     */
    protected $input_type = '';

    /**
     * Settings of the field
     *
     * @var string
     */
    protected $attributes = '';

    public function __construct( $name, $type )
    {
        $this->attributes = $this->get_attributes_default();

        $this->set_name( $name );
        $this->set_type( $type );
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
     * Get the name of the field
     *
     * @return string
     */
    public function set_name( $name )
    {
        $this->attributes[ 'name' ] = $name;
    }

    /**
     * Get field type
     *
     * @return string
     */
    public function get_type()
    {
        return $this->input_type;
    }

    /**
     * Get the name of the field
     *
     * @return string
     */
    private function set_type( $type )
    {
        $this->input_type = $type;
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
            $this->get_attributes_default_for_class(),
            array(
                'template'    => '',
                'name'        => '',
                'label'       => '',
                'required'    => 'no',
                'id'          => 0,
                'width'       => 'large',
                'css'         => '',
                'placeholder' => '',
                'default'     => '',
                'size'        => 40,
                'help'        => '',
            )
        );
    }

    /**
     * Get the attributes of the field
     *
     * @return string
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes of the field
     *
     * @return string
     */
    public function set_settings( $attributes )
    {
        $this->attributes = array_merge( $this->get_attributes_default(), $attributes );
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

    /*-------------------------------------------------------------------------*/
    /*
     * HTML helper routines
     */

    public static function html_prefix( $name )
    {
        return PREFIX_HTML . $name;
    }

    public function spew_html( $form_id )
    {
        $name           = Field::html_prefix( $this->attributes['name']  );
        $label          = Field::html_prefix( $this->attributes['label'] );
        $css_container  = $this->css[ 'input-container' ];
        $css_row        = $this->css[ 'input-row' ];
        $css_input      = $this->css[ 'input' ];

        ?>
        <div class="<?php echo $css_container ?>">
            <p class="<?php echo $css_row ?> " id="<?php echo $name . '_field' ?>" data-priority="">
                <span class="<?php echo $css_input ?>">
                <?php $this->render($form_id) ?>
                </span>
            </p>
            <?php
            if ( !empty( $attributes['help'] ) )
            {
                ?>
                <span class="<?php echo Field::html_prefix('help'); ?>"><?php echo stripslashes( $attributes['help'] ); ?></span>
                <?php
            }
            ?>
        </div>
    <?php
    }
}
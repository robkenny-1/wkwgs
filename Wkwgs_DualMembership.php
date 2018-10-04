<?php
/*
    "Washington Koi & Water Garden Society" Copyright (C) 2018 Rob Kenny

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

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

include_once('PluginCore/Wkwgs_LifeCycle.php');

class Wkwgs_DualMembership extends Wkwgs_LifeCycle
{
    public function addActionsAndFilters()
    {
        // Customer
        add_action( 'woocommerce_after_add_to_cart_form',				array( $this, 'product_show_customized' ) );
        add_filter( 'woocommerce_add_cart_item_data',					array( $this, 'product_save_customized', 10, 3 ) );

        // Editor/Administrator
        add_filter( 'woocommerce_product_data_tabs',					array( $this, 'product_admin_add_tab' ) );
        add_action( 'woocommerce_product_data_panels',					array( $this, 'product_admin_show_customized_checkboxes' ) );
        add_action( 'woocommerce_process_product_meta',					array( $this, 'product_admin_save_customized_checkboxes' ) );
    }

    /**
     * Values to display on the product's edit page, under WK&WGS
     * @return array
     */
    protected function get_product_customized()
    {
        return array(
                'wkwgs_is_dual_membership' => array(

                    'type'				=> 'checkbox',
                    'label'				=> __( 'Include Dual email', 'wkwgs' ),
                    
                    // Fields to display on product page
                    'display_fields'	=> array(

                        'first_name'            => array(
                                'type'			=> 'text',
                                'label'			=> __( 'Second member\'s first Name', 'wkwgs' ),
                            ),
                        'last_name'             => array(
                                'type'			=> 'text',
                                'label'			=> __( 'Second member\'s last Name', 'wkwgs' ),
                            ),
                        'dual_membership_email' => array(
                                'type'			=> 'email',
                                'label'			=> __( 'Second member\'s email address', 'wkwgs' ),
                                'required'		=> true,
                                'validate'		=> array( 'email' ), // rules for client side validation
                            ),
                        'dual_membership_phone' =>
                            array(
                                'type'			=> 'tel',
                                'label'			=> __( 'Second member\'s phone', 'wkwgs' ),
                                'validate'		=> array( 'phone' ),
                            ),
                    )
                ),
        );
    }
  
    /*
     *********************************************************************************
     *  Code for Customer
     *********************************************************************************
     */

    /**
     * Helper routine to get list of attributes to display for the $product
     * @return array of product_meta definitions
     */
    private function get_enabled_customized($product)
    {
        $enabled = array();

        $fields = $this->get_product_customized();

        foreach ( $fields as $key => $value )
        {
            $is_meta_enabled = $product->get_meta($key);

            if ($is_meta_enabled == 1)
            {
                $enabled[$key] = $value;
            }
        }
        return $enabled;
    }

    /**
     * Display the product's custom fields on product page
     * @return null
     */
    public function product_show_customized()
    {
        global $product;

        $enabled = $this->get_enabled_customized($product);

        if (empty($enabled))
        {
            return;
        }

        ?> 
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <div class="options_group">
                <?php
                foreach ( $enabled as $product_meta => $product_meta_args )
                {
                    $display_fields = $product_meta_args['display_fields'];

                    foreach ( $display_fields as $field => $field_args )
                    {
                        woocommerce_form_field(
                            $field,
                            $field_args);
                    }
                }
                ?>
            </div>
        </div>
        <?php

    }
 
    /**
     * Save the product's custom field values when added to the cart
     * @return null
     */
    public function product_save_customized($cart_item_data, $product_id, $variation_id, $quantity)
    {
        $product = wc_get_product( $post_id );

        $custom = $this->get_enabled_customized($product);

        if (empty($custom))
        {
            return;
        }
        
		$data = Wkwgs_DualMembership::get_form_data( $custom, $_POST );

        foreach ($data as $key => $value )
        {
            $cart_item_data[ $key ] = $value;
        }
    }

    /*
     *********************************************************************************
     *  Code for Manager/Admin
     *********************************************************************************
     */

    /**
        * Create the custom tab
        * @see     https://github.com/woocommerce/woocommerce/blob/e1a82a412773c932e76b855a97bd5ce9dedf9c44/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
        * @param   $tabs
        * @since   1.0.0
        */
    public function product_admin_add_tab( $tabs )
    {
        $tabs['wkwgs'] = array(
            'label'         => __( 'WK & WGS', 'wkwgs' ),	// The name of your panel
            'target'        => 'wkwgs_product_panel',		// Will be used to create an anchor link so needs to be unique
            'class'         => array( 'wkwgs_tab', 'show_if_simple', 'show_if_variable' ), // Class for your panel tab - helps hide/show depending on product type
            'priority'      => 80, // Where your panel will appear. By default, 70 is last item
        );
        return $tabs;
    }

    /**
    * Add the enable button for custom fields
    * @return null
    */
    public function product_admin_show_customized_checkboxes()
    {
        global $post;
        $fields = $this->get_product_customized();
        ?> 
        <div id='wkwgs_product_panel' class='panel woocommerce_options_panel'>
            <div class="options_group">
                <?php
                foreach ( $fields as $key => $field_args )
                {
                    $product = wc_get_product($post);
                    $value = $product->get_meta($key, true);

                    woocommerce_form_field(
                        $key,
                        $field_args,
                        $value);
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * extract POST data, perform data cleansing
     */
    private static function extract_data_from_post($type, $key, $post)
    {
        $value = '';

		switch ( $type )
        {
			case 'checkbox':
				$value = isset( $post[ $key ] ) ? 1 : 0; // WPCS: input var ok, CSRF ok.
				break;
			
            case 'multiselect':
				$value = isset( $post[ $key ] ) ? implode( ', ', wc_clean( wp_unslash( $post[ $key ] ) ) ) : ''; // WPCS: input var ok, CSRF ok.
				break;
			
            case 'textarea':
				$value = isset( $post[ $key ] ) ? wc_sanitize_textarea( wp_unslash( $post[ $key ] ) ) : ''; // WPCS: input var ok, CSRF ok.
				break;
			
            case 'password':
				$value = isset( $post[ $key ] ) ? wp_unslash( $post[ $key ] ) : ''; // WPCS: input var ok, CSRF ok, sanitization ok.
				break;

            case 'raw':
				$value = $post[ $key ];
				break;

            case 'text':
			default:
				$value = isset( $post[ $key ] ) ? wc_clean( $post[ $key ] ) : ''; // WPCS: input var ok, CSRF ok.
				break;
		}
    
        return $value;
    }
 
    private static function get_form_data( $form, $post )
    {
        $data = null;

		foreach ( $form as $key => $field )
        {
			$type = sanitize_title( isset( $field['type'] ) ? $field['type'] : 'text' );

            // Get sanitized data from post
            $value = Wkwgs_DualMembership::extract_data_from_post($type, $key, $post);

			// Required fields
			if ( ! empty( $field['required'] ) && empty( $value ) )
            {
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $field['label'] ), 'error' );
			}

            // Validate
			if ( ! empty( $value ) )
            {
				// Validation rules.
				if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
					foreach ( $field['validate'] as $rule ) {
						switch ( $rule ) {
							case 'postcode' :
								$post[ $key ] = strtoupper( str_replace( ' ', '', $post[ $key ] ) );

								if ( ! WC_Validation::is_postcode( $post[ $key ], $post[ $load_address . '_country' ] ) ) {
									wc_add_notice( __( 'Please enter a valid postcode / ZIP.', 'woocommerce' ), 'error' );
								} else {
									$post[ $key ] = wc_format_postcode( $post[ $key ], $post[ $load_address . '_country' ] );
								}
								break;
							case 'phone' :
								if ( ! WC_Validation::is_phone( $post[ $key ] ) ) {
									wc_add_notice( sprintf( __( '%s is not a valid phone number.', 'woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								}
								break;
							case 'email' :
								$post[ $key ] = strtolower( $post[ $key ] );

								if ( ! is_email( $post[ $key ] ) ) {
									wc_add_notice( sprintf( __( '%s is not a valid email address.', 'woocommerce' ), '<strong>' . $field['label'] . '</strong>' ), 'error' );
								}
								break;
						}
					}
				}
			}

            $data[ $key ] = $value;
        }
        return $data;
    }

    /**
        * Save the enable button for custom fields
        * @param $post_id
        * @since 1.0.0
        */
    public function product_admin_save_customized_checkboxes( $post_id )
    {
		$data = Wkwgs_DualMembership::get_form_data( $this->get_product_customized(), $_POST );

        $product = wc_get_product( $post_id );

        foreach ($data as $key => $value )
        {
            $product->update_meta_data( $key, $value );
        }

        $product->save(); 

    }
}

<?php

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}

include_once('PluginCore/Wkwgs_LifeCycle.php');

class Wkwgs_DualMembership extends Wkwgs_LifeCycle
{

    public function addActionsAndFilters()
    {
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // Create the custom tab
        add_filter( 'woocommerce_product_data_tabs',					array( $this, 'product_data_tabs' ) );
        
		// Add the custom fields
        add_action( 'woocommerce_product_data_panels',					array( $this, 'product_data_panels' ) );
        
		// Save the custom fields
        add_action( 'woocommerce_process_product_meta',					array( $this, 'process_product_meta' ) );

		// Show the fields on the product, just above the add to cart button
        add_action( 'woocommerce_before_add_to_cart_button',			array( $this, 'before_add_to_cart_button' ) );
    }
  
	/*
	 *********************************************************************************
	 *  Code for Customer
	 *********************************************************************************
	 */

	public function get_enabled_meta($product)
	{
		$enabled = array();

		$fields = $this->get_product_meta();

		foreach ( $fields as $key => $value )
		{
		    $is_meta_enabled = $product->get_meta($key, true);

			if ($is_meta_enabled === '1')
			{
				$enabled[$key] = $value;
			}
		}
		return $enabled;
	}
	public function before_add_to_cart_button()
	{
		global $product;

		$enabled = $this->get_enabled_meta($product);

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
 
	/*
	 *********************************************************************************
	 *  Code for Manager/Admin
	 *********************************************************************************
	 */

    /**
        * Add the new tab to the $tabs array
        * @see     https://github.com/woocommerce/woocommerce/blob/e1a82a412773c932e76b855a97bd5ce9dedf9c44/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
        * @param   $tabs
        * @since   1.0.0
        */
    public function product_data_tabs( $tabs )
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
	 * Values to display on the product's edit page, under WK&WGS
	 * @return array
	 */
	public function get_product_meta()
	{
		return array(
				'wkwgs_is_dual_membership' => array(

					// These product_meta should all be boolean values
					'type'				=> 'checkbox',
					'label'				=> __( 'Include Dual email', 'wkwgs' ),
					'getpost'			=> sanitize_text_field( isset( $_POST['wkwgs_is_dual_membership'] ) ? "1" : "0" ),
					
					// Fields to display on product page
					'display_fields'	=> array(

						'first_name' =>
							array(
								'type'			=> 'text',
								'label'			=> __( 'Second member\'s first Name', 'wkwgs' ),
							),
						'last_name' =>
							array(
								'type'			=> 'text',
								'label'			=> __( 'Second member\'s last Name', 'wkwgs' ),
							),
						'DualMembership_email' =>
							array(
								'type'			=> 'email',
								'label'			=> __( 'Second member\'s email address', 'wkwgs' ),
								'required'		=> true,
								//'validate'		=> array( 'email' ),
							),
						'DualMembership_phone' =>
							array(
								'type'			=> 'phone',
								'label'			=> __( 'Second member\'s phone', 'wkwgs' ),
								//'validate'		=> array( 'phone' ),
							),
					)
				),
		);
	}

	/**
    * Display fields for the new panel
    */
    public function product_data_panels()
	{
        global $post;
		$fields = $this->get_product_meta();
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
        * Save the custom fields using CRUD method
        * @param $post_id
        * @since 1.0.0
        */
    public function process_product_meta( $post_id )
	{ 
        $product = wc_get_product( $post_id );
 		
		$fields = $this->get_product_meta();
		foreach ( $fields as $key => $field_args )
		{
			$value = $field_args['getpost'];
	        $product->update_meta_data( $key, $value );		
		}

        $product->save();
 
    }
}

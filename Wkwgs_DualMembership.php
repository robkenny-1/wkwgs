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
        add_filter( 'woocommerce_product_data_tabs',	array( $this, 'product_option_create_tab' ) );
        // Add the custom fields
        add_action( 'woocommerce_product_data_panels',	array( $this, 'product_option_display_fields' ) );
        // Save the custom fields
        add_action( 'woocommerce_process_product_meta', array( $this, 'product_option_save_fields' ) );
    }

 
    /**
        * Add the new tab to the $tabs array
        * @see     https://github.com/woocommerce/woocommerce/blob/e1a82a412773c932e76b855a97bd5ce9dedf9c44/includes/admin/meta-boxes/class-wc-meta-box-product-data.php
        * @param   $tabs
        * @since   1.0.0
        */
    public function product_option_create_tab( $tabs )
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
	public function get_product_option()
	{
		return array(
				'wkwgs_dual_membership' => array(
					'type'		=> 'checkbox',
					'label'		=> __( 'Include Dual email', 'wkwgs' ),
					'getpost'	=> sanitize_text_field( isset( $_POST['wkwgs_dual_membership'] ) ? "1" : "0" ),
				),
		);
	}
	 
	/**
	 * Values to display on the product's store page
	 * @return array
	 */
	public function get_store_fields()
	{
		return array(
				'first_name' =>
					array(
						'type'			=> 'text',
						'label'			=> __( 'First Name', 'wkwgs' ),
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
						//'validate'		=> array( 'email' ),
					),
				'DualMembership_phone' =>
					array(
						'type'			=> 'phone',
						'label'			=> __( 'Second member\'s phone', 'wkwgs' ),
						//'validate'		=> array( 'phone' ),
					),
		);
	}

	/**
    * Display fields for the new panel
    */
    public function product_option_display_fields()
	{
        global $post;
		$fields = $this->get_product_option();
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
    public function product_option_save_fields( $post_id )
	{ 
        $product = wc_get_product( $post_id );
 		
		$fields = $this->get_product_option();
		foreach ( $fields as $key => $field_args )
		{
			$value = $field_args['getpost'];
	        $product->update_meta_data( $key, $value );		
		}
/*
		// Save the settings
		$key = 'include_DualMembership';
        $include_DualMembership = isset( $_POST[$key] ) ? 'yes' : 'no';
        $product->update_meta_data( $key, sanitize_text_field( $include_DualMembership ) );
 
        // Save the include_giftwrap_option setting
        $include_custom_message = isset( $_POST['include_custom_message'] ) ? 'yes' : 'no';
        $product->update_meta_data( 'include_custom_message', sanitize_text_field( $include_custom_message ) );
 
        // Save the giftwrap_cost setting
        $giftwrap_cost = isset( $_POST['giftwrap_cost'] ) ? $_POST['giftwrap_cost'] : '';
        $product->update_meta_data( 'giftwrap_cost', sanitize_text_field( $giftwrap_cost ) );
 */
        $product->save();
 
    }
}

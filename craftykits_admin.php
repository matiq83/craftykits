<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class that will hold functionality for plugin activation
 *
 * PHP version 5
 *
 * @category   Admin Side Code
 * @package    Crafty Kits
 * @author     Muhammad Atiq
 * @version    1.0.0
 * @since      File available since Release 1.0.0
*/

class CRAFTYKITS_Admin
{
    //Admin side starting point. Will call appropriate admin side hooks
    public function __construct() {

        add_action( 'admin_menu', array( $this, 'craftykits_admin_menus' ) );  
        
        add_action( 'craftykits_shipping_add_form_fields', array( $this, 'craftykits_shipping_add_form_fields' ), 10 );
        add_action( 'craftykits_shipping_edit_form_fields', array( $this, 'craftykits_shipping_edit_form_fields' ), 10, 1 );
        
        add_action( 'craftykits_coupons_add_form_fields', array( $this, 'craftykits_coupons_add_form_fields' ), 10 );
        add_action( 'craftykits_coupons_edit_form_fields', array( $this, 'craftykits_coupons_edit_form_fields' ), 10, 1 );
        
        add_action( 'edited_craftykits_shipping', array( $this, 'craftykits_save_custom_fields' ), 10, 1 );  
        add_action( 'create_craftykits_shipping', array( $this, 'craftykits_save_custom_fields' ), 10, 1 );
        add_action( 'edited_craftykits_coupons', array( $this, 'craftykits_save_custom_fields' ), 10, 1 );  
        add_action( 'create_craftykits_coupons', array( $this, 'craftykits_save_custom_fields' ), 10, 1 );
        
        add_action( 'add_meta_boxes', array( $this, 'craftykits_products_add_meta_box' ) );
        add_action( 'save_post', array( $this, 'craftykits_save_meta_box_data' ), 10, 1 );
        
        add_action( 'add_meta_boxes', array( $this, 'craftykits_orders_add_meta_box' ) );        
        
        add_filter( 'manage_edit-craftykits_product_columns', array( $this, 'craftykits_products_columns' ) ) ;
        add_action( 'manage_craftykits_product_posts_custom_column', array( $this, 'craftykits_products_columns_values' ), 10, 2 );
        
        add_filter( 'manage_edit-craftykits_orders_columns', array( $this, 'craftykits_orders_columns' ) ) ;
        add_action( 'manage_craftykits_orders_posts_custom_column', array( $this, 'craftykits_orders_columns_values' ), 10, 2 );
    }
    
    public function craftykits_orders_columns( $columns ) {
        
        $columns = array(
		'cb' => '<input type="checkbox" />',
                'title' => __( 'Order' ),
                'crafty_thumbnail' => __( 'Photo' ),
		'order_by' => __( 'Customer' ),
                'paypal_transaction' => __( 'PayPal Transaction ID' ),
		'date' => __( 'Date' )
	);

	return $columns;
    }
    
    public function craftykits_orders_columns_values( $column, $post_id ) {
        
        global $post;
        
        switch( $column ) {
            case 'crafty_thumbnail':
                $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );
                echo '<a href="' . $large_image_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '" target="_blank" >';
                echo get_the_post_thumbnail( $post_id, 'craftykits_thumbnail' ); 
                echo '</a>';
                break;
            case 'order_by' :
                $craftykits_order_customer = get_post_meta( $post->ID, 'craftykits_order_customer', true );
                $user_info = get_userdata( $craftykits_order_customer );
                if( $user_info->first_name != "" && $user_info->last_name != "" ){
                    $user_name = $user_info->first_name." ".$user_info->last_name;
                }else if( $user_info->first_name ){
                    $user_name = $user_info->first_name;
                }else if( $user_info->last_name ){
                    $user_name = $user_info->last_name;
                }else{
                    $user_name = $user_info->display_name;
                }
                
                echo '<a href="user-edit.php?user_id='.$craftykits_order_customer.'">'.$user_name.'</a>';
                
                break;
            case 'paypal_transaction' :
                $craftykits_paypal_transaction_id = get_post_meta( $post->ID, 'craftykits_paypal_transaction_id', true );
                echo $craftykits_paypal_transaction_id;
                break;
            /* Just break out of the switch statement for everything else. */
            default :
                    break;
        }
    }
    
    public function craftykits_products_columns( $columns ) {
        
        $columns = array(
		'cb' => '<input type="checkbox" />',
                'title' => __( 'Product' ),
                'price' => __( 'Price' ),
		'shipping_methods' => __( 'Shipping Methods' ),
                'coupons_codes' => __( 'Coupon Codes' ),
		'date' => __( 'Date' )
	);

	return $columns;
    }
    
    public function craftykits_products_columns_values( $column, $post_id ) {
        
        global $post;
        
        $craftykits_front = new CRAFTYKITS_Front();
        
	switch( $column ) {
                
            case 'price' :

                $craftykits_regular_price = get_post_meta( $post->ID, 'craftykits_regular_price', true );
                $craftykits_sale_price = get_post_meta( $post->ID, 'craftykits_sale_price', true );
                if( $craftykits_sale_price != "" ) {
                    echo '<strike>'.$craftykits_regular_price.'</strike> '.$craftykits_sale_price;
                }else{
                    echo $craftykits_regular_price;
                }
                break;

            case 'shipping_methods' :

                $terms = $craftykits_front->craftykits_get_shipping_methods( $post_id );

                /* If terms were found. */
                if ( !empty( $terms ) ) {

                    $out = array();

                    /* Loop through each term, linking to the 'edit posts' page for the specific term. */
                    foreach ( $terms as $term ) {
                            $out[] = sprintf( '<a href="edit-tags.php?action=edit&taxonomy=craftykits_shipping&tag_ID='.$term->term_id.'&post_type=craftykits_product">%s</a>',
                                    esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'craftykits_product', 'display' ) )
                            );
                    }

                    /* Join the terms, separating them with a comma. */
                    echo join( ', ', $out );
                }

                /* If no terms were found, output a default message. */
                else {
                    echo '<a href="edit-tags.php?taxonomy=craftykits_shipping&post_type=craftykits_product">'.__( 'All Shipping Methods' ).'</a>';
                }

                break;

            case 'coupons_codes' :

                $terms = $craftykits_front->craftykits_get_coupons( $post_id );

                /* If terms were found. */
                if ( !empty( $terms ) ) {

                    $out = array();

                    /* Loop through each term, linking to the 'edit posts' page for the specific term. */
                    foreach ( $terms as $term ) {
                            $out[] = sprintf( '<a href="edit-tags.php?action=edit&taxonomy=craftykits_coupons&tag_ID='.$term->term_id.'&post_type=craftykits_product">%s</a>',
                                    esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'craftykits_product', 'display' ) )
                            );
                    }

                    /* Join the terms, separating them with a comma. */
                    echo join( ', ', $out );
                }

                /* If no terms were found, output a default message. */
                else {
                    echo '<a href="edit-tags.php?taxonomy=craftykits_coupons&post_type=craftykits_product">'.__( 'All Coupon Codes' ).'</a>';
                }

                break;

            /* Just break out of the switch statement for everything else. */
            default :
                    break;
	}
    }
    
    public function craftykits_admin_menus(){
        
        add_submenu_page( 'edit.php?post_type=craftykits_product', CRAFTYKITS_PLUGIN_NAME.' Settings', 'Settings', 'manage_options', 'craftykits_settings', array( $this, 'craftykits_settings' ) );
    }    
    
    public function craftykits_orders_add_meta_box() {
        
        add_meta_box(
			'craftykits_order_customer',
			__( 'Order Customer', 'craftykits' ),
			array( $this, 'craftykits_customer_meta_box_callback' ),
			'craftykits_orders',
                        'side'
		);	
    }
    
    public function craftykits_customer_meta_box_callback( $post ) {
        
        // Add a nonce field so we can check for it later.
	wp_nonce_field( 'craftykits_meta_box', 'craftykits_meta_box_nonce' );
        
        $craftykits_order_customer = get_post_meta( $post->ID, 'craftykits_order_customer', true );

	echo '<label class="craftykits_order_customer_label">';
	_e( 'Select Order Customer', 'craftykits' );
	echo '</label> ';
        $blogusers = get_users( 'blog_id=1&orderby=nicename' );
        
	echo '<select id="craftykits_order_customer" name="craftykits_order_customer">';
        echo '<option value="">Select Customer</option>';
        foreach( $blogusers as $user ) {
            $user_info = get_userdata($user->ID);
            if( $user_info->first_name != "" && $user_info->last_name != "" ){
                $user_name = $user_info->first_name." ".$user_info->last_name;
            }else if( $user_info->first_name ){
                $user_name = $user_info->first_name;
            }else if( $user_info->first_name ){
                $user_name = $user_info->last_name;
            }else{
                $user_name = $user->display_name;
            }
            if( $craftykits_order_customer == $user->ID ) {
                echo '<option value="'.$user->ID.'" selected>'.$user_name.'</option>';
            }else{
                echo '<option value="'.$user->ID.'">'.$user_name.'</option>';
            }
        }
        echo '</select>';
        
        echo '<div class="craftykits_clear_both"></div>';
    }
    
    public function craftykits_products_add_meta_box() {
        
        add_meta_box(
			'craftykits_product_info',
			__( 'Product Information', 'craftykits' ),
			array( $this, 'craftykits_products_meta_box_callback' ),
			'craftykits_product'
		);	
    }
    
    public function craftykits_products_meta_box_callback( $post ) {
        
        // Add a nonce field so we can check for it later.
	wp_nonce_field( 'craftykits_meta_box', 'craftykits_meta_box_nonce' );
        
        $craftykits_regular_price = get_post_meta( $post->ID, 'craftykits_regular_price', true );

	echo '<label class="craftykits_regular_price_field">';
	_e( 'Regular Price ($)', 'craftykits' );
	echo '</label> ';
	echo '<input type="text" id="craftykits_regular_price" name="craftykits_regular_price" value="' . esc_attr( $craftykits_regular_price ) . '" size="25" />';
        
        $craftykits_sale_price = get_post_meta( $post->ID, 'craftykits_sale_price', true );
        
        echo '<div class="craftykits_clear_both"></div>';
        
	echo '<label class="craftykits_sale_price_field">';
	_e( 'Sales Price ($)', 'craftykits' );
	echo '</label> ';
	echo '<input type="text" id="craftykits_sale_price" name="craftykits_sale_price" value="' . esc_attr( $craftykits_sale_price ) . '" size="25" />';
        
        echo '<div class="craftykits_clear_both"></div>';
    }
    
    public function craftykits_save_meta_box_data( $post_id ) {
        
        /*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['craftykits_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['craftykits_meta_box_nonce'], 'craftykits_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Make sure that it is set.
	if ( isset( $_POST['craftykits_regular_price'] ) ) {
            
            // Sanitize user input.
            $craftykits_regular_price = sanitize_text_field( $_POST['craftykits_regular_price'] );

            // Update the meta field in the database.
            update_post_meta( $post_id, 'craftykits_regular_price', $craftykits_regular_price );
	}

	if ( isset( $_POST['craftykits_regular_price'] ) ) {
            
            // Sanitize user input.
            $craftykits_sale_price = sanitize_text_field( $_POST['craftykits_sale_price'] );

            // Update the meta field in the database.
            update_post_meta( $post_id, 'craftykits_sale_price', $craftykits_sale_price );
        }
        
        if ( isset( $_POST['craftykits_order_customer'] ) ) {
            
            // Sanitize user input.
            $craftykits_order_customer = sanitize_text_field( $_POST['craftykits_order_customer'] );
            
            // Update the meta field in the database.
            update_post_meta( $post_id, 'craftykits_order_customer', $craftykits_order_customer );
        }
    }
    
    public function craftykits_save_custom_fields( $term_id ) {
        
        if ( isset( $_POST['craftykits_shipping_price'] ) ) {
            $t_id = $term_id;
            // Save the option array.
            update_option( "craftykits_shipping_price_".$t_id, $_POST['craftykits_shipping_price'] );
	}
        
        if ( isset( $_POST['craftykits_coupon_discount_type'] ) ) {
            $t_id = $term_id;
            // Save the option array.
            update_option( "craftykits_coupon_discount_type_".$t_id, $_POST['craftykits_coupon_discount_type'] );
	}
        
        if ( isset( $_POST['craftykits_coupon_discount_value'] ) ) {
            $t_id = $term_id;
            // Save the option array.
            update_option( "craftykits_coupon_discount_value_".$t_id, $_POST['craftykits_coupon_discount_value'] );
	}
    }
    
    public function craftykits_coupons_add_form_fields() {
        ?>
        <div class="form-field">
            <label for="craftykits_coupon_discount_type"><?php _e( 'Discount Type', 'craftykits' ); ?></label>
            <select name="craftykits_coupon_discount_type" id="craftykits_coupon_discount_type">
                <option value="flat">Flat Discount</option>
                <option value="percentage">% Discount</option>
            </select>
            <p class="description"><?php _e( 'Select discount type', 'craftykits' ); ?></p>
	</div>
        <div class="form-field">
            <label for="craftykits_coupon_discount_value"><?php _e( 'Discount Value', 'craftykits' ); ?></label>
            <input type="text" name="craftykits_coupon_discount_value" id="craftykits_coupon_discount_value" value="">
            <p class="description"><?php _e( 'Put discount value', 'craftykits' ); ?></p>
	</div>
        <?php
    }
    
    public function craftykits_coupons_edit_form_fields( $term ) {
        
        // put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$craftykits_coupon_discount_type = get_option( "craftykits_coupon_discount_type_".$t_id );
        $craftykits_coupon_discount_value = get_option( "craftykits_coupon_discount_value_".$t_id );
        
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="craftykits_coupon_discount_type"><?php _e( 'Discount Type', 'craftykits' ); ?></label></th>
            <td>
                <select name="craftykits_coupon_discount_type" id="craftykits_coupon_discount_type">
                    <?php if( $craftykits_coupon_discount_type == "flat" ) {?>
                    <option value="flat" selected="">Flat Discount</option>
                    <?php }else{?>
                    <option value="flat">Flat Discount</option>
                    <?php }?>
                    
                    <?php if( $craftykits_coupon_discount_type == "percentage" ) {?>
                    <option value="percentage" selected="">% Discount</option>
                    <?php }else{?>
                    <option value="percentage">% Discount</option>
                    <?php }?>
                </select>
                <p class="description"><?php _e( 'Select discount type', 'craftykits' ); ?></p>
            </td>
	</tr>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="craftykits_coupon_discount_value"><?php _e( 'Discount Type', 'craftykits' ); ?></label></th>
            <td>
                <label for="craftykits_coupon_discount_value"><?php _e( 'Discount Value', 'craftykits' ); ?></label>
                <input type="text" name="craftykits_coupon_discount_value" id="craftykits_coupon_discount_value" value="<?php echo $craftykits_coupon_discount_value; ?>">
                <p class="description"><?php _e( 'Put discount value', 'craftykits' ); ?></p>
            </td>
	</tr>
        <?php
    }
    
    public function craftykits_shipping_edit_form_fields( $term ) {
        
        // put the term ID into a variable
	$t_id = $term->term_id;
 
	// retrieve the existing value(s) for this meta field. This returns an array
	$craftykits_shipping_price = get_option( "craftykits_shipping_price_".$t_id );
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="craftykits_shipping_price"><?php _e( 'Shipping Price', 'craftykits' ); ?></label></th>
            <td>
                <input type="text" name="craftykits_shipping_price" id="craftykits_shipping_price" value="<?php echo esc_attr( $craftykits_shipping_price ) ? esc_attr( $craftykits_shipping_price ) : ''; ?>">
                <p class="description"><?php _e( 'Enter shipping price in this field', 'craftykits' ); ?></p>
            </td>
	</tr>
        <?php
    }
    
    public function craftykits_shipping_add_form_fields() {
        ?>
        <div class="form-field">
            <label for="craftykits_shipping_price"><?php _e( 'Shipping Price ($)', 'craftykits' ); ?></label>
            <input type="text" name="craftykits_shipping_price" id="craftykits_shipping_price" value="">
            <p class="description"><?php _e( 'Enter shipping price in this field', 'craftykits' ); ?></p>
	</div>
        <?php
    }
    
    public function craftykits_settings() {
        
        wp_enqueue_script('media-upload');
    	wp_enqueue_script('thickbox');
    	wp_enqueue_style('thickbox');
        
        if( isset($_POST['btnsave']) && $_POST['btnsave'] != "" ) {
            
            $exclude = array('btnsave');
            $options = array();
            
            foreach( $_POST as $k => $v ) {
                if( !in_array( $k, $exclude )) {
                    $options[$k] = $v;
                }
            }
            
            update_option( 'craftykits_settings', $options );
            $message = 'Settings Saved Successfully!';
        }
        
        $craftykits_front = new CRAFTYKITS_Front();
        
        $products = $craftykits_front->craftykits_get_products();
        
        $pages = get_pages(); 
        
        $options = get_option( 'craftykits_settings' );
        
        require_once CRAFTYKITS_PLUGIN_PATH.'templates/admin/settings.php';
        $this->load_javascript();
    }
        
    public function craftykits_reports() {
        
    }
    
    private function load_javascript() {?>
        <script language="javascript">
            jQuery(document).ready(function($) {
                attach_click_to_media_button('file','upload_media_button');
            });

            function attach_click_to_media_button(type,obj_class){
                jQuery('.'+obj_class).unbind( 'click' );
                jQuery('.'+obj_class).click(function() {
                    upload_media_button =true;
                    formfieldID=jQuery(this).prev().attr("id");
                    formfield = jQuery("#"+formfieldID).attr('name');
                    tb_show('', 'media-upload.php?type='+type+'&amp;TB_iframe=true');
                    if(upload_media_button==true){
                        var oldFunc = window.send_to_editor;
                        window.send_to_editor = function(html) {
                            if(type=='image'){
                                fileURL = jQuery('img', html).attr('src');
                            }else{
                                fileURL = jQuery(html).attr('href');
                            }
                            jQuery("#"+formfieldID).val(fileURL);
                            tb_remove();
                            window.send_to_editor = oldFunc;
                        }
                    }
                    upload_media_button=false;
                });
            }
        </script>
        <?php
	}
}

$craftykits_admin = new CRAFTYKITS_Admin();
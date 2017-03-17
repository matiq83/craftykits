<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Class that will hold functionality for plugin activation
 *
 * PHP version 5
 *
 * @category   Front Side Code
 * @package    Crafty Kits
 * @author     Muhammad Atiq
 * @version    1.0.0
 * @since      File available since Release 1.0.0
*/

class CRAFTYKITS_Front
{
    //Front side starting point. Will call appropriate front side hooks
    public function __construct() {
        
        add_action( 'init', array( $this, 'craftykits_post_type' ), 12 );
        add_action( 'init', array( $this, 'craftykits_taxonomies' ), 10 );
        add_action( 'wp_head', array( $this, 'craftykits_header' ), 5 );
        add_action( 'template_redirect', array( $this, 'craftykits_template_redirect' ) );
        
        add_shortcode( 'craftykits_finalize_order', array( $this, 'craftykits_finalize_order' ));
        add_shortcode( 'craftykits_user_payment_info', array( $this, 'craftykits_user_payment_info' ) );
        add_shortcode( 'craftykits_upload_photo', array( $this, 'craftykits_upload_photo' ));
        add_shortcode( 'craftykits_user_info', array( $this, 'craftykits_user_info' ));
        
        add_action( 'after_setup_theme', array( $this, 'craftykits_theme_setup' ) );       
        
    }
    
    private function craftykits_paypal_save_cc( $cc_info = array() ) {
        if( empty( $cc_info ) ) {
            return false;
        }
        require_once CRAFTYKITS_PLUGIN_PATH.'includes/paypal_save_cc.php'; 
        
        if ( empty( $error )) {
            return array( 'status' => 'true', 'data' => $data );
        }else{
            return array( 'status' => 'false', 'data' => $error );
        }
    }
    
    private function craftykits_create_order_post( $data ) {
        
        if( empty( $data )) {
            return false;
        }
        
        $post_title = time();
        
        $post_content = '';
        
        $post_content.='<h1>Order Detail</h1>';
        $post_content.= '<strong>Product:</strong> '.$data['product_title'].'<br>';
        $post_content.= '<strong>Product Price:</strong> $'.$data['unit_price'].'<br>';
        $post_content.= '<strong>Product Quantity:</strong> '.$data['product_quantity'].'<br>';
        $post_content.= '<strong>Product Total Price:</strong> $'.$data['total_price'].'<br>';
        $post_content.= '<strong>Shipping Price:</strong> $'.$data['shipping_price'].'<br>';
        $post_content.= '<strong>Grand Price:</strong> $'.$data['grand_price'];
        
        $post_content.='<h1>Shipping Details</h1>';
        $post_content.='<strong>Name:</strong> '.$data['craftykits_user_shipping_name'].'<br>';
        $post_content.='<strong>Address1:</strong> '.$data['craftykits_user_shipping_address1'].'<br>';
        $post_content.='<strong>Address2:</strong> '.$data['craftykits_user_shipping_address2'].'<br>';
        $post_content.='<strong>City:</strong> '.$data['craftykits_user_shipping_city'].'<br>';
        $post_content.='<strong>State:</strong> '.$data['craftykits_user_shipping_state'].'<br>';
        $post_content.='<strong>Zip:</strong> '.$data['craftykits_user_shipping_zip'].'<br>';
        $post_content.='<strong>Country:</strong> '.$data['craftykits_user_shipping_country'];
        
        $post_content.='<h1>Billing Details</h1>';
        $post_content.='<strong>Name:</strong> '.$data['craftykits_user_billing_name'].'<br>';
        $post_content.='<strong>Address1:</strong> '.$data['craftykits_user_billing_address1'].'<br>';
        $post_content.='<strong>Address2:</strong> '.$data['craftykits_user_billing_address2'].'<br>';
        $post_content.='<strong>City:</strong> '.$data['craftykits_user_billing_city'].'<br>';
        $post_content.='<strong>State:</strong> '.$data['craftykits_user_billing_state'].'<br>';
        $post_content.='<strong>Zip:</strong> '.$data['craftykits_user_billing_zip'].'<br>';
        $post_content.='<strong>Country:</strong> '.$data['craftykits_user_billing_country'];
        
        if( $data['user_comments'] != "" ) {
            $post_content.='<h1>Customer Note</h1>';
            $post_content.=$data['user_comments'];
        }
        
        $post = array();
        
        $post['post_title'] = $post_title;
        $post['post_content'] = $post_content;
        $post['post_status'] = 'publish';
        $post['post_type'] = 'craftykits_orders';
        
        $order_id = wp_insert_post( $post );
        
        return $order_id;
    }
    
    private function craftykits_create_tmp_order( $cc_info, $order_data, $user_meta ) {
        if( empty( $order_data ) || empty( $user_meta ) ) {
            return false;
        }
       
        $product = get_post( $order_data['product_id'] );
        
        $data = array();
        $data['product_title'] = $product->post_title;
        $data['unit_price'] = $order_data['unit_price'];
        $data['product_quantity'] = $order_data['product_quantity'];
        $data['total_price'] = $order_data['total_price'];
        $data['shipping_price'] = $order_data['shipping_price'];
        $data['grand_price'] = $order_data['grand_price'];
        $data['craftykits_user_shipping_name'] = $user_meta['craftykits_user_shipping_name'];
        $data['craftykits_user_shipping_address1'] = $user_meta['craftykits_user_shipping_address1'];
        $data['craftykits_user_shipping_address2'] = $user_meta['craftykits_user_shipping_address2'];
        $data['craftykits_user_shipping_city'] = $user_meta['craftykits_user_shipping_city'];
        $data['craftykits_user_shipping_state'] = $user_meta['craftykits_user_shipping_state'];
        $data['craftykits_user_shipping_zip'] = $user_meta['craftykits_user_shipping_zip'];
        $data['craftykits_user_shipping_country'] = $user_meta['craftykits_user_shipping_country'];
        $data['craftykits_user_billing_name'] = $user_meta['craftykits_user_billing_name'];
        $data['craftykits_user_billing_address1'] = $user_meta['craftykits_user_billing_address1'];
        $data['craftykits_user_billing_address2'] = $user_meta['craftykits_user_billing_address2'];
        $data['craftykits_user_billing_city'] = $user_meta['craftykits_user_billing_city'];
        $data['craftykits_user_billing_state'] = $user_meta['craftykits_user_billing_state'];
        $data['craftykits_user_billing_zip'] = $user_meta['craftykits_user_billing_zip'];
        $data['craftykits_user_billing_country'] = $user_meta['craftykits_user_billing_country'];
        $data['user_comments'] = $order_data['user_comments'];
        
        $order_id = $this->craftykits_create_order_post( $data );
        
        if( !$order_id ) {
            wp_delete_post( $order_id, TRUE );
            return array( 'status' => 'false', 'data' => 'Unable to create order post' );
        }else{
            $post = array(
                'ID'           => $order_id,
                'post_title'   => "Order #".$order_id
            );
            // Update the post into the database
            wp_update_post( $post );

            $payment_info['cc_token'] = $user_meta['craftykits_cc_id'];
            $payment_info['amount'] = $order_data['grand_price'];
            $payment_info['detail'] = "Order #".$order_id;

            add_post_meta( $order_id, '_thumbnail_id', $order_data['attachement_id'], true );
            add_post_meta( $order_id, 'craftykits_order_customer', $order_data['user_id'], true );
            return array( 'status' => 'true', 'data' => $order_id );
        }
    }
    
    private function craftykits_create_order( $cc_info, $order_data, $user_meta ) {
        if( empty( $order_data ) || empty( $user_meta ) ) {
            return false;
        }
       
        $product = get_post( $order_data['product_id'] );
        
        $data = array();
        $data['product_title'] = $product->post_title;
        $data['unit_price'] = $order_data['unit_price'];
        $data['product_quantity'] = $order_data['product_quantity'];
        $data['total_price'] = $order_data['total_price'];
        $data['shipping_price'] = $order_data['shipping_price'];
        $data['grand_price'] = $order_data['grand_price'];
        $data['craftykits_user_shipping_name'] = $user_meta['craftykits_user_shipping_name'];
        $data['craftykits_user_shipping_address1'] = $user_meta['craftykits_user_shipping_address1'];
        $data['craftykits_user_shipping_address2'] = $user_meta['craftykits_user_shipping_address2'];
        $data['craftykits_user_shipping_city'] = $user_meta['craftykits_user_shipping_city'];
        $data['craftykits_user_shipping_state'] = $user_meta['craftykits_user_shipping_state'];
        $data['craftykits_user_shipping_zip'] = $user_meta['craftykits_user_shipping_zip'];
        $data['craftykits_user_shipping_country'] = $user_meta['craftykits_user_shipping_country'];
        $data['craftykits_user_billing_name'] = $user_meta['craftykits_user_billing_name'];
        $data['craftykits_user_billing_address1'] = $user_meta['craftykits_user_billing_address1'];
        $data['craftykits_user_billing_address2'] = $user_meta['craftykits_user_billing_address2'];
        $data['craftykits_user_billing_city'] = $user_meta['craftykits_user_billing_city'];
        $data['craftykits_user_billing_state'] = $user_meta['craftykits_user_billing_state'];
        $data['craftykits_user_billing_zip'] = $user_meta['craftykits_user_billing_zip'];
        $data['craftykits_user_billing_country'] = $user_meta['craftykits_user_billing_country'];
        $data['user_comments'] = $order_data['user_comments'];
        
        $order_id = $this->craftykits_create_order_post( $data );
        
        if( !$order_id ) {
            return array( 'status' => 'false', 'data' => 'Unable to create order post' );
        }
        
        $post = array(
            'ID'           => $order_id,
            'post_title'   => "Order #".$order_id
        );
        // Update the post into the database
        wp_update_post( $post );
  
        $payment_info['cc_token'] = $user_meta['craftykits_cc_id'];
        $payment_info['amount'] = $order_data['grand_price'];
        $payment_info['detail'] = "Order #".$order_id;
        
        add_post_meta( $order_id, '_thumbnail_id', $order_data['attachement_id'], true );
        add_post_meta( $order_id, 'craftykits_order_customer', $order_data['user_id'], true );
        
        require_once CRAFTYKITS_PLUGIN_PATH.'includes/paypal_make_payment.php'; 
        
        if ( empty( $error )) {
            add_post_meta( $order_id, 'craftykits_paypal_transaction_id', $data->id, true );
            return array( 'status' => 'true', 'data' => $data, 'order_id' => $order_id );
        }else{
            wp_delete_post( $order_id, TRUE );
            return array( 'status' => 'false', 'data' => $error );
        }        
    }
    
    public function craftykits_finalize_order() {
        
        $order_id = $_GET['order_id'];
        $payment_type = $_GET['payment']?$_GET['payment']:'cc';
        
        if( !is_numeric($order_id) && $payment_type == 'cc' ) {
            $this->redirect(CRAFTYKITS_SITE_BASE_URL);
        }
        
        $this->craftykits_send_notifications( $order_id );        
    }
    
    private function craftykits_send_notifications( $order_id ) {
        
        if( empty($order_id) ) {
            return false;
        }
        
        $order_mails_sent = get_post_meta( $order_id, 'craftykits_order_mail_sent', true );
        
        if( $order_mails_sent == 'yes' ) {
            return true;
        }
        
        $order = get_post( $order_id );
        $options = get_option( 'craftykits_settings' );
        $user_id = get_post_meta( $order_id, 'craftykits_order_customer', true );
        $customer_info = get_userdata($user_id);

        $headers= 'From: '.get_bloginfo( 'name' ).' <'.get_bloginfo( 'admin_email' ).'>';
        add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));

        $subject = $options['craftykits_customer_order_email_subject'];
        $subject = str_replace( '[siteurl]', CRAFTYKITS_SITE_BASE_URL, $subject );
        $subject = str_replace( '[buyername]', $customer_info->user_login, $subject );
        $subject = str_replace( '[buyeremail]', $customer_info->user_email, $subject );
        $subject = str_replace( '[order_id]', $order_id, $subject );

        $message = $options['craftykits_customer_order_email_body'];
        $message = str_replace( '[siteurl]', CRAFTYKITS_SITE_BASE_URL, $message );
        $message = str_replace( '[buyername]', $customer_info->user_login, $message );
        $message = str_replace( '[buyeremail]', $customer_info->user_email, $message );
        $message = str_replace( '[order_id]', $order_id, $message );
        $message = str_replace( '[order_detail]', $order->post_content, $message );

        $to = $customer_info->user_email;

        wp_mail( $to, $subject, $message, $headers );

        $subject = $options['craftykits_admin_order_email_subject'];
        $subject = str_replace( '[siteurl]', CRAFTYKITS_SITE_BASE_URL, $subject );
        $subject = str_replace( '[buyername]', $customer_info->user_login, $subject );
        $subject = str_replace( '[buyeremail]', $customer_info->user_email, $subject );
        $subject = str_replace( '[order_id]', $order_id, $subject );

        $message = $options['craftykits_admin_order_email_body'];
        $message = str_replace( '[siteurl]', CRAFTYKITS_SITE_BASE_URL, $message );
        $message = str_replace( '[buyername]', $customer_info->user_login, $message );
        $message = str_replace( '[buyeremail]', $customer_info->user_email, $message );
        $message = str_replace( '[order_id]', $order_id, $message );
        $message = str_replace( '[order_detail]', $order->post_content, $message );

        $to = get_bloginfo( 'admin_email' );

        wp_mail( $to, $subject, $message, $headers );

        add_post_meta( $order_id, 'craftykits_order_mail_sent', 'yes', true );
    }
    
    public function craftykits_theme_setup() {
        
        add_image_size( 'craftykits_medium', 700 );
        add_image_size( 'craftykits_thumbnail', 100 );
    }
    
    public function craftykits_header() {
        $step2_page = get_permalink( $this->get_page_by_shortcode( '[craftykits_user_info]' ) );
        if( strpos( $step2_page, "?" ) === FALSE ) {
            $step2_page = $step2_page."?";            
        }else{
            $step2_page = $step2_page."&";            
        }
        echo '<script type="text/javascript">';
        echo 'var craftykits_file_uplaod = "'.CRAFTYKITS_SITE_BASE_URL.'/?craftykits_file_uplaod=yes";';
        echo 'var craftykits_file_uplaod_step2 = "'.$step2_page.'";';
        //echo 'var craftykits_ajax_url = "'.admin_url( 'admin-ajax.php' ).'";';
        echo '</script>';
    }
    
    public function craftykits_user_info() {
        if( $_GET['paypal_cancel_return'] == 'true' && is_numeric( $_GET['order_id'] )) {
            $postid = $_GET['order_id'];
            wp_delete_post( $postid, TRUE );
        }
        
        $hash = $_GET['hash'];
        
        if( $hash == "" ) {
            return '<div class="craftykits_userinfo_container"><p>You Should First Upload Some Photo To Access This Page!</p></div>';
        }
        
        $html = '';
        
        $step3_page = get_permalink( $this->get_page_by_shortcode( '[craftykits_user_payment_info]' ) );
        
        $attachement_id = base64_decode( $hash );
        $craftykits_upload_img = wp_get_attachment_image( $attachement_id, 'craftykits_medium' );
        
        $current_user = wp_get_current_user();
        $user_meta = array();
        if ( 0 == $current_user->ID ) {
            $user_name = 'New User';
            $shipping_countries = $this->get_countries();
            $billing_countries = $shipping_countries;
        }else{
            if( $current_user->user_firstname != "" && $current_user->user_lastname !="" ) {
                $user_name = $current_user->user_firstname." ".$current_user->user_lastname;
            }elseif( $current_user->user_firstname != "" ) {
                $user_name = $current_user->user_firstname;
            }elseif( $current_user->user_lastname !="" ) {
                $user_name = $current_user->user_lastname;
            }else{
                $user_name = $current_user->display_name;
            }
            
            $user_meta = get_user_meta( $current_user->ID, 'craftykits_user_data', true );
            if( $user_meta == "" ) {
                $shipping_countries = $this->get_countries();
                $billing_countries = $shipping_countries;
            }else{
                
                $shipping_countries = $this->get_countries( $user_meta['craftykits_user_shipping_country'] );
                $billing_countries = $this->get_countries( $user_meta['craftykits_user_billing_country'] );                
            }            
        }
        
        $craftykits_settings = get_option( 'craftykits_settings' );
        $craftykits_regular_price = get_post_meta( $craftykits_settings['craftykits_default_product'], 'craftykits_regular_price', true );
        $craftykits_sale_price = get_post_meta( $craftykits_settings['craftykits_default_product'], 'craftykits_sale_price', true );
        $terms_page_url = get_permalink( $craftykits_settings['craftykits_terms_page'] );
        
        if( $craftykits_sale_price != "" ) {
            $default_price = '<strike>$'.$craftykits_regular_price.'</strike> $'.$craftykits_sale_price;
        }else{
            $default_price = $craftykits_regular_price;
        }
        
        ob_start();
        require_once CRAFTYKITS_PLUGIN_PATH.'templates/front/user_info.php';
        $html = ob_get_clean();
        
        return $html;
    }
    
    public function craftykits_template_redirect() {
        if( $_GET['craftykits_file_uplaod'] == 'yes' ) {
            if( $_REQUEST['action'] == 'validate_register_username' ) {
                if( $_REQUEST['fieldValue'] != "" ) {
                    if ( !username_exists( $_REQUEST['fieldValue'] ) ) {
                        echo json_encode( array( $_REQUEST['fieldId'], true ));
                    }else{
                        echo json_encode( array( $_REQUEST['fieldId'], false ));
                    }
                }else{
                    echo json_encode( array( $_REQUEST['fieldId'], false, '* This field is required' ));
                }
            }elseif( $_REQUEST['action'] == 'validate_register_user_email' ){
                if( $_REQUEST['fieldValue'] != "" ) {
                    if ( !email_exists( $_REQUEST['fieldValue'] ) ) {
                        echo json_encode( array( $_REQUEST['fieldId'], true ));
                    }else{
                        echo json_encode( array( $_REQUEST['fieldId'], false ));
                    }
                }else{
                    echo json_encode( array( $_REQUEST['fieldId'], false, '* This field is required' ));
                }                
            }elseif( $_REQUEST['action'] == 'ajaxlogin' ){
                $info = array();
                $info['user_login'] = $_POST['username'];
                $info['user_password'] = $_POST['password'];
                $info['remember'] = true;
                $user_signon = wp_signon( $info, false );
                
                if ( is_wp_error($user_signon) ){
                    echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
                } else {
                    $user_meta = get_user_meta( $user_signon->ID, 'craftykits_user_data', true );
                    echo json_encode(array('loggedin'=>true, 'user_id'=> $user_signon->ID ));
                }        
            }elseif( $_REQUEST['action'] == 'apply_coupon' ){
                $craftykits_coupons = $this->craftykits_get_coupons( $_POST['product_id'] );
                $coupon_valid = false;
                foreach( $craftykits_coupons as $coupon ) {
                    if( $coupon->name == $_POST['coupon'] ) {
                        $coupon_valid = TRUE;
                        $craftykits_coupon_discount_type = get_option( "craftykits_coupon_discount_type_".$coupon->term_id );
                        $craftykits_coupon_discount_value = get_option( "craftykits_coupon_discount_value_".$coupon->term_id );
                        break;
                    }
                }
                if( $coupon_valid ) {
                    $grand_price = $_POST['grand_price'];
                    if( $craftykits_coupon_discount_type == "flat" ) {
                        $new_price = $grand_price - $craftykits_coupon_discount_value;
                        if( $new_price < 0 ) {
                            $new_price = 0;
                        }
                    }else{
                        $new_price = $grand_price - (($grand_price*$craftykits_coupon_discount_value)/100);
                    }
                    $new_price = number_format((float)$new_price, 2, '.', '');
                    echo json_encode(array('valid_coupon'=>true, 'price'=> $new_price, 'price_html' => '<strike>$'.$grand_price.'</strike> $'.$new_price, 'orignal_grand_price' => $grand_price ));
                }else{
                    echo json_encode(array('valid_coupon'=>false, 'message'=>__('Wrong coupon code provided.')));
                }
            }elseif( $_POST['action'] == 'craftykits_process_payment' ){
                $cc_info['type'] = $_POST['cc_type'];
                $cc_info['number'] = $_POST['cc'];
                $cc_info['exp_month'] = $_POST['cc_exp_month'];
                $cc_info['exp_year'] = $_POST['cc_exp_year'];
                $cc_info['cvv'] = $_POST['cvv'];
                
                $user_id = $_POST['craftykits_user_id'];
                $order_data = array();

                $order_data['user_comments'] = $_POST['customer_notes'];
                $order_data['attachement_id'] = $_POST['craftykits_upload_id'];
                $order_data['product_id'] = $_POST['craftykits_product_id'];
                $order_data['user_id'] = $user_id;
                $order_data['grand_price'] = $_POST['craftykits_cart_grand_price'];
                $order_data['unit_price'] = $_POST['product_price'];
                $order_data['total_price'] = $_POST['cart_total'];
                $order_data['shipping_price'] = $_POST['shipping_price'];
                $order_data['product_quantity'] = $_POST['product_quantity'];


                $user_meta = get_user_meta( $user_id, 'craftykits_user_data', true );

                $user_meta['cc_type'] = $card['data']->type;
                $user_meta['craftykits_cc_exp_month'] = $card['data']->expire_month;
                $user_meta['craftykits_cc_exp_year'] = $card['data']->expire_year;
                $user_meta['craftykits_cc_number_verification'] = $card['data']->cvv2;
                $user_meta['craftykits_cc_number'] = $card['data']->number;
                $user_meta['craftykits_cc_id'] = $card['data']->id;

                update_user_meta( $user_id, 'craftykits_user_data', $user_meta );

                $order = $this->craftykits_create_order( $cc_info, $order_data, $user_meta );

                if( $order['status'] == 'false' ) {
                    echo json_encode(array('valid_cc'=>false, 'message'=>__( $order['data']->details[0]->issue )));
                }else{
                    echo json_encode(array('valid_cc'=>true, 'order_id' => $order['order_id'] ));
                }
            }elseif( $_POST['action'] == 'craftykits_process_paypal_payment' ){
                $cc_info['type'] = $_POST['cc_type'];
                $cc_info['number'] = $_POST['cc'];
                $cc_info['exp_month'] = $_POST['cc_exp_month'];
                $cc_info['exp_year'] = $_POST['cc_exp_year'];
                $cc_info['cvv'] = $_POST['cvv'];
                
                $user_id = $_POST['craftykits_user_id'];
                $order_data = array();

                $order_data['user_comments'] = $_POST['customer_notes'];
                $order_data['attachement_id'] = $_POST['craftykits_upload_id'];
                $order_data['product_id'] = $_POST['craftykits_product_id'];
                $order_data['user_id'] = $user_id;
                $order_data['grand_price'] = $_POST['craftykits_cart_grand_price'];
                $order_data['unit_price'] = $_POST['product_price'];
                $order_data['total_price'] = $_POST['cart_total'];
                $order_data['shipping_price'] = $_POST['shipping_price'];
                $order_data['product_quantity'] = $_POST['product_quantity'];


                $user_meta = get_user_meta( $user_id, 'craftykits_user_data', true );

                $user_meta['cc_type'] = $card['data']->type;
                $user_meta['craftykits_cc_exp_month'] = $card['data']->expire_month;
                $user_meta['craftykits_cc_exp_year'] = $card['data']->expire_year;
                $user_meta['craftykits_cc_number_verification'] = $card['data']->cvv2;
                $user_meta['craftykits_cc_number'] = $card['data']->number;
                $user_meta['craftykits_cc_id'] = $card['data']->id;

                update_user_meta( $user_id, 'craftykits_user_data', $user_meta );

                $order = $this->craftykits_create_tmp_order( $cc_info, $order_data, $user_meta );

                if( $order['status'] == 'false' ) {
                    echo json_encode(array('valid_cc'=>false, 'message'=> $order['data']));
                }else{
                    echo json_encode(array('valid_cc'=>true, 'order_id' => $order['data'] ));
                }
            }elseif( $_REQUEST['paypal'] == 'process' && is_numeric( $_REQUEST['user'] ) ){
                $raw_post_data = file_get_contents('php://input');
                $raw_post_array = explode('&', $raw_post_data);
                $myPost = array();
                foreach ($raw_post_array as $keyval) {
                  $keyval = explode ('=', $keyval);
                  if (count($keyval) == 2)
                     $myPost[$keyval[0]] = urldecode($keyval[1]);
                }
                // read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
                $req = 'cmd=_notify-validate';
                if(function_exists('get_magic_quotes_gpc')) {
                   $get_magic_quotes_exists = true;
                } 
                foreach ($myPost as $key => $value) {        
                   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
                        $value = urlencode(stripslashes($value)); 
                   } else {
                        $value = urlencode($value);
                   }
                   $req .= "&$key=$value";
                }
                $craftykits_settings = get_option( 'craftykits_settings' );
                if( $craftykits_settings['craftykits_paypal_test_mode'] == 'yes' ) {
                    $paypal_form_action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';                    
                }else{
                    $paypal_form_action = 'https://www.secure.paypal.com/cgi-bin/webscr';
                }
                $ch = curl_init( $paypal_form_action );
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
                
                if( !($res = curl_exec($ch)) ) {
                    error_log("Got " . curl_error($ch) . " when processing IPN data");
                    curl_close($ch);
                    exit;
                }
                
                if (strcmp ($res, "VERIFIED") == 0) {
                    $txn_id = $_POST['txn_id'];
                    $order_id = $_POST['custom'];
                    add_post_meta( $order_id, 'craftykits_paypal_transaction_id', $txn_id, true );
                    $this->craftykits_send_notifications( $order_id );
                } else if (strcmp ($res, "INVALID") == 0) {
                    echo "The response from IPN was: <b>" .$res ."</b>";
                }

                curl_close($ch);

            }elseif( $_FILES['file']['name'] != "" ){
                if ( !function_exists( 'wp_generate_attachment_metadata' ) ) {
                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                    require_once( ABSPATH . 'wp-admin/includes/media.php' );
                }

                $uploadedfile = $_FILES['file'];

                $upload_overrides = array( 'test_form' => false );

                $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

                if ( $movefile && !isset( $movefile['error'] ) ) {
                    //$file_path = base64_encode( $movefile['file'] );
                    //$file_url = base64_encode( $movefile['url'] );

                    $filename = $uploadedfile['name'];

                    $wp_filetype = wp_check_filetype( $filename, null );

                    $attachment = array(
                        'post_mime_type' => $wp_filetype['type'],
                        'post_parent' => 0,
                        'post_title' => preg_replace('/\.[^.]+$/', '', $filename ),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );

                    $attachment_id = wp_insert_attachment( $attachment, $movefile['file'], 0 );

                    if ( !is_wp_error( $attachment_id ) ) {

                        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file'] );
                        wp_update_attachment_metadata( $attachment_id, $attachment_data );

                        echo json_encode(array( 'status' => 'ok', 'hash' => base64_encode( $attachment_id ) ));

                    }else{

                        echo json_encode(array( 'status' => 'error', 'msg' => $attachment_id['error'] ));
                    }

                } else {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */
                    echo json_encode(array( 'status' => 'error', 'msg' => $movefile['error'] ));                
                }
            }
            exit();
        }
    }
    
    public function craftykits_upload_photo() {
        
        $html = '';
        
        ob_start();
        require_once CRAFTYKITS_PLUGIN_PATH.'templates/front/upload_photo.php';
        $html = ob_get_clean();
        
        return $html;
    }
    
    public function craftykits_user_payment_info() {
        
        $attachement_id = 0;
        $user_id = 0;
        
        if( $_POST['btnSubmit'] != "" ) {
            $user_id = $_POST['craftykits_user_id'];
            $attachement_id = $_POST['craftykits_upload_id'];
            $arr_exclude = array( 'craftykits_upload_id', 'craftykits_user_id', 'btnSubmit' );
            $user_meta = get_user_meta( $user_id, 'craftykits_user_data', TRUE );
            
            if( empty( $user_meta ) ) {
                $user_meta = array();
            }
            foreach( $_POST as $key=>$val ) {
                if( !in_array( $key, $arr_exclude ) ) {
                    $user_meta[$key] = $val;
                }
            }
            if( $user_id != 0 ) {
                update_user_meta( $user_id, 'craftykits_user_data', $user_meta );
            }else{
                $user_email = $_POST['craftykits_user_email'];
                $user_name = $_POST['craftykits_register_user_name'];
                $password = $_POST['craftykits_register_user_password'];
                
                $user_id = username_exists( $user_name );
                if ( !$user_id && email_exists($user_email) == false ) {
                    $user_id = wp_create_user( $user_name, $password, $user_email );
                    $info = array();
                    $info['user_login'] = $user_name;
                    $info['user_password'] = $password;
                    $info['remember'] = true;
                    $user_signon = wp_signon( $info, false );
                }
                update_user_meta( $user_id, 'craftykits_user_data', $user_meta );
            }
        }
        
        if( $user_id == 0 || $attachement_id == 0 || empty( $user_meta ) ) {
            return '<div class="craftykits_userinfo_container"><p>You Should First Upload Some Photo To Access This Page!</p></div>';
        }
        
        $user_info = get_userdata( $user_id );
        
        wp_enqueue_style( 'craftykits_fancybox', CRAFTYKITS_PLUGIN_URL.'fancybox/jquery.fancybox.css' );
        wp_enqueue_script( 'craftykits_fancybox', CRAFTYKITS_PLUGIN_URL.'fancybox/jquery.fancybox.pack.js', array( 'jquery' ) );
        
        $craftykits_upload_img = wp_get_attachment_image( $attachement_id, 'craftykits_thumbnail' );
        
        $craftykits_settings = get_option( 'craftykits_settings' );
        
        $product_id = $craftykits_settings['craftykits_default_product'];
        
        $all_products = $this->craftykits_get_products();
        
        $craftykits_shipping_methods = $this->craftykits_get_shipping_methods( $product_id );
        
        $terms_page_url = get_permalink( $craftykits_settings['craftykits_terms_page'] );
        
        $step4_page = get_permalink( $this->get_page_by_shortcode( '[craftykits_finalize_order]' ) );
        
        $step2_page = get_permalink( $this->get_page_by_shortcode( '[craftykits_user_info]' ) );
        $attachement_hash = base64_encode( $attachement_id );
        if( strpos( $step2_page, "?" ) === FALSE ) {
            $step2_page = $step2_page."?hash=".$attachement_hash;            
        }else{
            $step2_page = $step2_page."&hash=".$attachement_hash;            
        }
        
        if( $craftykits_settings['craftykits_paypal_test_mode'] == 'yes' ) {
            $paypal_form_action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';            
        }else{
            $paypal_form_action = 'https://www.secure.paypal.com/cgi-bin/webscr';
        }
        
        $paypal_business = $craftykits_settings['craftykits_paypal_business'];
        
        $shipping_methods = array();
        
        foreach( $craftykits_shipping_methods as $shipping_method ) {
            
            $price = get_option( "craftykits_shipping_price_".$shipping_method->term_id );
            
            $shipping_methods[] = array( 'id' => $shipping_method->term_id, 'slug' => $shipping_method->slug, 'name' => $shipping_method->name, 'price' => $price );
        }
        
        $craftykits_regular_price = get_post_meta( $product_id, 'craftykits_regular_price', true );
        $craftykits_sale_price = get_post_meta( $product_id, 'craftykits_sale_price', true );
        if( $craftykits_sale_price != "" ) {
            $product_price = $craftykits_sale_price;
            $total_price = $shipping_methods[0]['price'] + $craftykits_sale_price;
        }else{
            $product_price = $craftykits_regular_price;
            $total_price = $shipping_methods[0]['price'] + $craftykits_regular_price;
        }
        
        $html = '';
        
        ob_start();
        require_once CRAFTYKITS_PLUGIN_PATH.'templates/front/user_payment_info.php';
        $html = ob_get_clean();
        
        return $html;
    }
    
    public function craftykits_get_products() {
        
        $args = array( 'posts_per_page' => -1, 'post_type' => 'craftykits_product' );
        
        $craftykits_products = get_posts( $args );
        
        return $craftykits_products;
    }
    
    public function craftykits_get_coupons( $post_id = '' ) {
        
        if( $post_id == '' ) {
            $craftykits_coupons = get_terms( array( 'craftykits_coupons' ), array( 'hide_empty' => false ) );
        }else{
            $craftykits_coupons = get_the_terms( $post_id, 'craftykits_coupons' ); 
            if( !$craftykits_coupons || is_wp_error( $craftykits_coupons ) ) {
                $craftykits_coupons = get_terms( array( 'craftykits_coupons' ), array( 'hide_empty' => false ) );
            }
        }
        
        return $craftykits_coupons;
    }
    
    public function craftykits_get_shipping_methods( $post_id = '' ) {
        
        if( $post_id == '' ) {
            $craftykits_shipping_methods = get_terms( array( 'craftykits_shipping' ), array( 'hide_empty' => false ) );
        }else{
            $craftykits_shipping_methods = get_the_terms( $post_id, 'craftykits_shipping' ); 
            if( !$craftykits_shipping_methods || is_wp_error( $craftykits_shipping_methods ) ) {
                $craftykits_shipping_methods = get_terms( array( 'craftykits_shipping' ), array( 'hide_empty' => false ) );
            }
        }
        
        return $craftykits_shipping_methods;
    }
    
    public function craftykits_taxonomies() {
        
        $labels = array(
		'name'              => __( 'Shipping Methods' ),
		'singular_name'     => __( 'Shipping Method' ),
		'search_items'      => __( 'Search Shipping Methods' ),
		'all_items'         => __( 'All Shipping Methods' ),
		'edit_item'         => __( 'Edit Shipping Method' ),
		'update_item'       => __( 'Update Shipping Method' ),
		'add_new_item'      => __( 'Add New Shipping Method' ),
		'new_item_name'     => __( 'New Shipping Method' ),
		'menu_name'         => __( 'Shipping Methods' )
	);
        
        $args = array(
		'public'            => true,
                'rewrite'           => false,
                'hierarchical'      => true,
		'labels'            => $labels		
	);
        
        register_taxonomy(
            'craftykits_shipping',
            'craftykits_product',
            $args
        );
        
        $labels = array(
		'name'              => __( 'Coupon Codes' ),
		'singular_name'     => __( 'Coupon Code' ),
		'search_items'      => __( 'Search Coupon Codes' ),
		'all_items'         => __( 'All Coupon Codes' ),
		'edit_item'         => __( 'Edit Coupon Code' ),
		'update_item'       => __( 'Update Coupon Code' ),
		'add_new_item'      => __( 'Add New Coupon Code' ),
		'new_item_name'     => __( 'New Coupon Code' ),
		'menu_name'         => __( 'Coupon Codes' )                
	);
        
        $args = array(
		'public'            => true,
                'rewrite'           => false,
                'hierarchical'      => true,
		'labels'            => $labels		
	);
        
        register_taxonomy(
            'craftykits_coupons',
            'craftykits_product',
            $args
        );
    }
    
    public function craftykits_post_type() {
        
        register_post_type( 'craftykits_product',
                            array(
                                'labels' => array(
                                    'name' => __( CRAFTYKITS_PLUGIN_NAME, 'craftykits' ),
                                    'singular_name' => __( 'Product', 'craftykits' ),
                                    'add_new' => __( 'Add New Product', 'craftykits' ),
                                    'add_new_item' => __( 'Add New Product', 'craftykits' ),
                                    'edit_item' => __( 'Edit Product', 'craftykits' ),
                                    'update_item' => __( 'Update Product', 'craftykits' ),
                                    'search_items' => __( 'Search Products', 'craftykits' ),
                                    'not_found' => __( 'No Product Found', 'craftykits' )
                                ),
                                'public' => true,
                                'has_archive' => true,
                                'taxonomies' => array( 'craftykits_shipping','craftykits_coupons'),
                                'supports' => array( 'title', 'editor', 'thumbnail' )
                            )
                          );  
        
        register_post_type( 'craftykits_orders',
                            array(
                                'labels' => array(
                                    'name' => __( 'Orders', 'craftykits' ),
                                    'singular_name' => __( 'Order', 'craftykits' ),
                                    'add_new' => __( 'Add New Order', 'craftykits' ),
                                    'add_new_item' => __( 'Add New Order', 'craftykits' ),
                                    'edit_item' => __( 'Edit Order', 'craftykits' ),
                                    'update_item' => __( 'Update Order', 'craftykits' ),
                                    'search_items' => __( 'Search Orders', 'craftykits' ),
                                    'not_found' => __( 'No Order Found', 'craftykits' )
                                ),
                                'public' => true,
                                'has_archive' => true,
                                'publicly_queryable' => false,
                                'supports' => array( 'title', 'editor', 'thumbnail' )
                            )
                          );
        
    }
    
    private function get_page_by_shortcode( $shortcode ) {
        global $wpdb;
        
        $sql = "SELECT ID FROM `".$wpdb->prefix."posts` WHERE `post_type` = 'page' AND `post_status` = 'publish' AND `post_content` LIKE '%".$shortcode."%'";
        $page_id = $wpdb->get_var( $sql );
        if( !is_numeric( $page_id )) {
            return 0;
        }
        return $page_id;
    }
    
    private function make_safe( $variable ) {

        $variable = $this->strip_html_tags($variable);
        $bad = array("=","<", ">", "/","\"","`","~","'","$","%","#");
        $variable = str_replace($bad, "", $variable);
        $variable = mysql_real_escape_string(trim($variable));

        return $variable;
    }

    private function strip_html_tags( $text ) {
        $text = preg_replace(
                array(
                  // Remove invisible content
                        '@<head[^>]*?>.*?</head>@siu',
                        '@<style[^>]*?>.*?</style>@siu',
                        '@<script[^>]*?.*?</script>@siu',
                        '@<object[^>]*?.*?</object>@siu',
                        '@<embed[^>]*?.*?</embed>@siu',
                        '@<applet[^>]*?.*?</applet>@siu',
                        '@<noframes[^>]*?.*?</noframes>@siu',
                        '@<noscript[^>]*?.*?</noscript>@siu',
                        '@<noembed[^>]*?.*?</noembed>@siu'
                ),
                array(
                        '', '', '', '', '', '', '', '', ''), $text );

        return strip_tags( $text);
    }

    // Function to safe redirect the page without warnings
    private function redirect( $url ) {
        echo '<script language="javascript">window.location.href="'.$url.'";</script>';
        exit();
    }
    
    private function get_countries( $selected_counry="United States", $get_options_html=true, $with_country_code = false ) {
        $countries = array("AF" => "Afghanistan",
                            "AX" => "Aland Islands",
                            "AL" => "Albania",
                            "DZ" => "Algeria",
                            "AS" => "American Samoa",
                            "AD" => "Andorra",
                            "AO" => "Angola",
                            "AI" => "Anguilla",
                            "AQ" => "Antarctica",
                            "AG" => "Antigua and Barbuda",
                            "AR" => "Argentina",
                            "AM" => "Armenia",
                            "AW" => "Aruba",
                            "AU" => "Australia",
                            "AT" => "Austria",
                            "AZ" => "Azerbaijan",
                            "BS" => "Bahamas",
                            "BH" => "Bahrain",
                            "BD" => "Bangladesh",
                            "BB" => "Barbados",
                            "BY" => "Belarus",
                            "BE" => "Belgium",
                            "BZ" => "Belize",
                            "BJ" => "Benin",
                            "BM" => "Bermuda",
                            "BT" => "Bhutan",
                            "BO" => "Bolivia",
                            "BA" => "Bosnia and Herzegovina",
                            "BW" => "Botswana",
                            "BV" => "Bouvet Island",
                            "BR" => "Brazil",
                            "IO" => "British Indian Ocean Territory",
                            "BN" => "Brunei Darussalam",
                            "BG" => "Bulgaria",
                            "BF" => "Burkina Faso",
                            "BI" => "Burundi",
                            "KH" => "Cambodia",
                            "CM" => "Cameroon",
                            "CA" => "Canada",
                            "CV" => "Cape Verde",
                            "KY" => "Cayman Islands",
                            "CF" => "Central African Republic",
                            "TD" => "Chad",
                            "CL" => "Chile",
                            "CN" => "China",
                            "CX" => "Christmas Island",
                            "CC" => "Cocos (Keeling) Islands",
                            "CO" => "Colombia",
                            "KM" => "Comoros",
                            "CG" => "Congo",
                            "CD" => "Congo, The Democratic Republic of The",
                            "CK" => "Cook Islands",
                            "CR" => "Costa Rica",
                            "CI" => "Cote D'ivoire",
                            "HR" => "Croatia",
                            "CU" => "Cuba",
                            "CY" => "Cyprus",
                            "CZ" => "Czech Republic",
                            "DK" => "Denmark",
                            "DJ" => "Djibouti",
                            "DM" => "Dominica",
                            "DO" => "Dominican Republic",
                            "EC" => "Ecuador",
                            "EG" => "Egypt",
                            "SV" => "El Salvador",
                            "GQ" => "Equatorial Guinea",
                            "ER" => "Eritrea",
                            "EE" => "Estonia",
                            "ET" => "Ethiopia",
                            "FK" => "Falkland Islands (Malvinas)",
                            "FO" => "Faroe Islands",
                            "FJ" => "Fiji",
                            "FI" => "Finland",
                            "FR" => "France",
                            "GF" => "French Guiana",
                            "PF" => "French Polynesia",
                            "TF" => "French Southern Territories",
                            "GA" => "Gabon",
                            "GM" => "Gambia",
                            "GE" => "Georgia",
                            "DE" => "Germany",
                            "GH" => "Ghana",
                            "GI" => "Gibraltar",
                            "GR" => "Greece",
                            "GL" => "Greenland",
                            "GD" => "Grenada",
                            "GP" => "Guadeloupe",
                            "GU" => "Guam",
                            "GT" => "Guatemala",
                            "GG" => "Guernsey",
                            "GN" => "Guinea",
                            "GW" => "Guinea-bissau",
                            "GY" => "Guyana",
                            "HT" => "Haiti",
                            "HM" => "Heard Island and Mcdonald Islands",
                            "VA" => "Holy See (Vatican City State)",
                            "HN" => "Honduras",
                            "HK" => "Hong Kong",
                            "HU" => "Hungary",
                            "IS" => "Iceland",
                            "IN" => "India",
                            "ID" => "Indonesia",
                            "IR" => "Iran, Islamic Republic of",
                            "IQ" => "Iraq",
                            "IE" => "Ireland",
                            "IM" => "Isle of Man",
                            "IL" => "Israel",
                            "IT" => "Italy",
                            "JM" => "Jamaica",
                            "JP" => "Japan",
                            "JE" => "Jersey",
                            "JO" => "Jordan",
                            "KZ" => "Kazakhstan",
                            "KE" => "Kenya",
                            "KI" => "Kiribati",
                            "KP" => "Korea, Democratic People's Republic of",
                            "KR" => "Korea, Republic of",
                            "KW" => "Kuwait",
                            "KG" => "Kyrgyzstan",
                            "LA" => "Lao People's Democratic Republic",
                            "LV" => "Latvia",
                            "LB" => "Lebanon",
                            "LS" => "Lesotho",
                            "LR" => "Liberia",
                            "LY" => "Libyan Arab Jamahiriya",
                            "LI" => "Liechtenstein",
                            "LT" => "Lithuania",
                            "LU" => "Luxembourg",
                            "MO" => "Macao",
                            "MK" => "Macedonia, The Former Yugoslav Republic of",
                            "MG" => "Madagascar",
                            "MW" => "Malawi",
                            "MY" => "Malaysia",
                            "MV" => "Maldives",
                            "ML" => "Mali",
                            "MT" => "Malta",
                            "MH" => "Marshall Islands",
                            "MQ" => "Martinique",
                            "MR" => "Mauritania",
                            "MU" => "Mauritius",
                            "YT" => "Mayotte",
                            "MX" => "Mexico",
                            "FM" => "Micronesia, Federated States of",
                            "MD" => "Moldova, Republic of",
                            "MC" => "Monaco",
                            "MN" => "Mongolia",
                            "ME" => "Montenegro",
                            "MS" => "Montserrat",
                            "MA" => "Morocco",
                            "MZ" => "Mozambique",
                            "MM" => "Myanmar",
                            "NA" => "Namibia",
                            "NR" => "Nauru",
                            "NP" => "Nepal",
                            "NL" => "Netherlands",
                            "AN" => "Netherlands Antilles",
                            "NC" => "New Caledonia",
                            "NZ" => "New Zealand",
                            "NI" => "Nicaragua",
                            "NE" => "Niger",
                            "NG" => "Nigeria",
                            "NU" => "Niue",
                            "NF" => "Norfolk Island",
                            "MP" => "Northern Mariana Islands",
                            "NO" => "Norway",
                            "OM" => "Oman",
                            "PK" => "Pakistan",
                            "PW" => "Palau",
                            "PS" => "Palestinian Territory, Occupied",
                            "PA" => "Panama",
                            "PG" => "Papua New Guinea",
                            "PY" => "Paraguay",
                            "PE" => "Peru",
                            "PH" => "Philippines",
                            "PN" => "Pitcairn",
                            "PL" => "Poland",
                            "PT" => "Portugal",
                            "PR" => "Puerto Rico",
                            "QA" => "Qatar",
                            "RE" => "Reunion",
                            "RO" => "Romania",
                            "RU" => "Russian Federation",
                            "RW" => "Rwanda",
                            "SH" => "Saint Helena",
                            "KN" => "Saint Kitts and Nevis",
                            "LC" => "Saint Lucia",
                            "PM" => "Saint Pierre and Miquelon",
                            "VC" => "Saint Vincent and The Grenadines",
                            "WS" => "Samoa",
                            "SM" => "San Marino",
                            "ST" => "Sao Tome and Principe",
                            "SA" => "Saudi Arabia",
                            "SN" => "Senegal",
                            "RS" => "Serbia",
                            "SC" => "Seychelles",
                            "SL" => "Sierra Leone",
                            "SG" => "Singapore",
                            "SK" => "Slovakia",
                            "SI" => "Slovenia",
                            "SB" => "Solomon Islands",
                            "SO" => "Somalia",
                            "ZA" => "South Africa",
                            "GS" => "South Georgia and The South Sandwich Islands",
                            "ES" => "Spain",
                            "LK" => "Sri Lanka",
                            "SD" => "Sudan",
                            "SR" => "Suriname",
                            "SJ" => "Svalbard and Jan Mayen",
                            "SZ" => "Swaziland",
                            "SE" => "Sweden",
                            "CH" => "Switzerland",
                            "SY" => "Syrian Arab Republic",
                            "TW" => "Taiwan, Province of China",
                            "TJ" => "Tajikistan",
                            "TZ" => "Tanzania, United Republic of",
                            "TH" => "Thailand",
                            "TL" => "Timor-leste",
                            "TG" => "Togo",
                            "TK" => "Tokelau",
                            "TO" => "Tonga",
                            "TT" => "Trinidad and Tobago",
                            "TN" => "Tunisia",
                            "TR" => "Turkey",
                            "TM" => "Turkmenistan",
                            "TC" => "Turks and Caicos Islands",
                            "TV" => "Tuvalu",
                            "UG" => "Uganda",
                            "UA" => "Ukraine",
                            "AE" => "United Arab Emirates",
                            "GB" => "United Kingdom",
                            "US" => "United States",
                            "UM" => "United States Minor Outlying Islands",
                            "UY" => "Uruguay",
                            "UZ" => "Uzbekistan",
                            "VU" => "Vanuatu",
                            "VE" => "Venezuela",
                            "VN" => "Viet Nam",
                            "VG" => "Virgin Islands, British",
                            "VI" => "Virgin Islands, U.S.",
                            "WF" => "Wallis and Futuna",
                            "EH" => "Western Sahara",
                            "YE" => "Yemen",
                            "ZM" => "Zambia",
                            "ZW" => "Zimbabwe");
        
        if( !$get_options_html ) {
            return $countries;
        }
        
        $options = '';
        
        foreach( $countries as $code=>$country ) {
            
            if( $with_country_code ) {
                if( $selected_counry == $country || $selected_counry == $code ) {
                    $options.='<option value="'.$code.'" selected>'.$country.'</option>';
                }else{
                    $options.='<option value="'.$code.'">'.$country.'</option>';
                }
            }else{
                if( $selected_counry == $country || $selected_counry == $code ) {
                    $options.='<option value="'.$country.'" selected>'.$country.'</option>';
                }else{
                    $options.='<option value="'.$country.'">'.$country.'</option>';
                }                
            }
        }
        
        return $options;
    }
}

$craftykits_front = new CRAFTYKITS_Front();
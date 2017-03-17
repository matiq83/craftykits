<div class="craftykits_userinfo_container craftykits_userpayement_container">
    <form name="frm_craftykits_userinfo" id="frm_craftykits_userinfo" action="<?php echo $step4_page;?>" method="post">
    <div class="craftykits_user_cart">
        <table width="96%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td></td>
                    <td>Product</td>
                    <td>Quantity</td>
                    <td>Unit Price</td>
                </tr>
            </thead>
            <tr>
                <td><a href="<?php echo wp_get_attachment_url($attachement_id);?>" class="craftykits_product_cart_img"><?php echo $craftykits_upload_img; ?></a></td>
                <td>
                    <select name="craftykits_product" id="craftykits_product">
                    <?php 
                    foreach ( $all_products as $product ) {
                        $regular_price = get_post_meta( $product->ID, 'craftykits_regular_price', true );
                        $sale_price = get_post_meta( $product->ID, 'craftykits_sale_price', true );
                        if( $sale_price != "" ) {
                            $price = $craftykits_sale_price;                            
                        }else{
                            $price = $regular_price;                            
                        }
                        if( $product->ID == $product_id ) {
                            echo '<option value="'.$product->ID.'@@'.$price.'" selected>'.$product->post_title.'</option>';
                        }else{
                            echo '<option value="'.$product->ID.'@@'.$price.'">'.$product->post_title.'</option>';
                        }
                    }
                    ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="craftykits_product_quantity" id="craftykits_product_quantity" value="1" min="1" max="500">                    
                </td>
                <td class="craftykits_user_cart_unit_price">$<?php echo $product_price;?></td>
            </tr>
        </table>
    </div>
    
    <div class="craftykits_clear_both"></div>
    <br>
    
    <div class="craftykits_col_onthird">
        <div class="craftykits_user_comments_box craftykits_fileds_box">
            <div class="box_title"><span>Note to Recipient</span></div>
            <div class="box_content">
                <div class="box_field">
                    <textarea name="craftykits_customer_notes" id="craftykits_customer_notes"></textarea>
                </div>
            </div>
        </div>
        <div class="craftykits_clear_both"></div>
        <div class="craftykits_user_pshipping_box craftykits_fileds_box">
            <div class="box_title"><span>Shipping Address</span><input type="button" onclick="javascript: window.location.href = craftykits_file_uplaod_step2+'hash=<?php echo base64_encode($attachement_id);?>';" class="btn" value="Edit?" />
            <div class="craftykits_clear_both_no_margin"></div>
            </div>
            <div class="box_content">
                <div class="box_field">
                    <span>Name:</span> <?php echo $user_meta['craftykits_user_shipping_name'];?></div><div class="box_field">
                    <span>Address1:</span> <?php echo $user_meta['craftykits_user_shipping_address1'];?></div><div class="box_field">
                    <span>City:</span> <?php echo $user_meta['craftykits_user_shipping_city'];?></div><div class="box_field">
                    <span>State:</span> <?php echo $user_meta['craftykits_user_shipping_state'];?></div><div class="box_field">
                    <span>Zip:</span> <?php echo $user_meta['craftykits_user_shipping_zip'];?></div><div class="box_field">
                    <span>Country:</span> <?php echo $user_meta['craftykits_user_shipping_country'];?>
                </div>
            </div>
        </div>
        <div class="craftykits_clear_both"></div>
        <div class="craftykits_user_pbilling_box craftykits_fileds_box">
            <div class="box_title"><span>Billing Address</span><input type="button" onclick="javascript: window.location.href = craftykits_file_uplaod_step2+'hash=<?php echo base64_encode($attachement_id);?>';" class="btn" value="Edit?" />
            <div class="craftykits_clear_both_no_margin"></div>
            </div>
            <div class="box_content">
                <div class="box_field">
                    <span>Name:</span> <?php echo $user_meta['craftykits_user_billing_name'];?></div><div class="box_field">
                    <span>Address1:</span> <?php echo $user_meta['craftykits_user_billing_address1'];?></div><div class="box_field">
                    <span>City:</span> <?php echo $user_meta['craftykits_user_billing_city'];?></div><div class="box_field">
                    <span>State:</span> <?php echo $user_meta['craftykits_user_billing_state'];?></div><div class="box_field">
                    <span>Zip:</span> <?php echo $user_meta['craftykits_user_billing_zip'];?></div><div class="box_field">
                    <span>Country:</span> <?php echo $user_meta['craftykits_user_billing_country'];?>
                </div>
            </div>
        </div>
        <div class="craftykits_clear_both"></div>
    </div>
    
    <div class="craftykits_col_onthird">
        <div class="craftykits_user_shipping_methods_box craftykits_fileds_box">
            <div class="box_title"><span>Shipping Method</span></div>
            <div class="box_content">
                <?php foreach( $shipping_methods as $shipping_method ) {?>
                <div class="box_field">
                    <?php if( $shipping_method['id'] == $shipping_methods[0]['id'] ) {?>
                    <input type="radio" name="craftykits_shipping" id="craftykits_shipping_<?php echo $shipping_method['id'];?>" checked value="<?php echo $shipping_method['price'];?>" >
                    <?php }else{?>
                    <input type="radio" name="craftykits_shipping" id="craftykits_shipping_<?php echo $shipping_method['id'];?>" value="<?php echo $shipping_method['price'];?>" >
                    <?php }?>
                    <?php echo $shipping_method['name'];?>
                    <span>$<?php echo $shipping_method['price'];?> (US)</span>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="craftykits_clear_both"></div>
        <div class="craftykits_user_coupons_box craftykits_fileds_box">
            <div class="box_title"><span>Have a Coupon?</span></div>
            <div class="box_content">
                <div class="box_field">
                    <input type="text" name="craftykits_coupons" id="craftykits_coupons" value="">
                    <input type="button" class="btn btn-primary" value="Apply Coupon" name="btn_coupon" id="btn_coupon" onclick="javascript: craftykits_submit_coupon();"/>
                    <img src="<?php echo CRAFTYKITS_PLUGIN_URL;?>/images/ajax-loader.gif" id="craftykits_ajax_img" >
                </div>
            </div>
        </div>
        <div class="craftykits_clear_both"></div>
        <div class="craftykits_user_payment_box craftykits_fileds_box">
            <div class="box_title"><span>Payment Information</span></div>
            <div class="craftykits_payment_options">
                <div class="craftykits_payment_option_container">
                    <input type="radio" name="craftykits_payment_option" id="craftykits_payment_cc" value="cc" checked="checked" />
                    <div class="craftykits_payment_cc">
                        <img src="<?php echo CRAFTYKITS_PLUGIN_URL;?>/images/cc.gif" id="craftykits_payment_cc" />
                    </div>
                </div>
                <div class="craftykits_payment_option_container">
                    <input type="radio" name="craftykits_payment_option" id="craftykits_payment_paypal" value="paypal" />
                    <div class="craftykits_payment_paypal">

                        <img src="<?php echo CRAFTYKITS_PLUGIN_URL;?>/images/paypal.jpg" id="craftykits_payment_paypal" />
                    </div>
                </div>
            </div>
            <div class="craftykits_clear_both"></div>
            <div class="box_content">
                <div class="box_field">
                    <label>Card Expiry Date</label>
                    <select name="craftykits_cc_exp_month" id="craftykits_cc_exp_month" data-validation-engine="validate[required]">
                        <option value="">Month</option>
                        <?php 
                        for( $i=1; $i<=12; $i++ ) {
                            if( $i<= 9 ) {
                                echo '<option value="0'.$i.'">0'.$i.'</option>';                                
                            }else{
                                echo '<option value="'.$i.'">'.$i.'</option>';                                
                            }
                        }
                        ?>
                    </select>
                    <select name="craftykits_cc_exp_year" id="craftykits_cc_exp_year" data-validation-engine="validate[required]">
                        <option value="">Year</option>
                        <?php 
                        for( $i=date('Y'); $i<=date('Y')+12; $i++ ) {
                            echo '<option value="'.$i.'">'.$i.'</option>';                            
                        }
                        ?>
                    </select>
                </div>
                <div class="box_field">
                    <label>Card Number</label>
                    <input type="text" name="craftykits_cc_number" id="craftykits_cc_number" value="" data-validation-engine="validate[required,creditCard]">
                </div>
                <div class="box_field">
                    <label>Card Verification</label>
                    <input type="text" name="craftykits_cc_number_verification" id="craftykits_cc_number_verification" value="" maxlength="4" data-validation-engine="validate[required,custom[onlyNumberSp]]">
                </div>
            </div>
        </div>
        <div class="craftykits_clear_both"></div>
    </div>
    <div class="craftykits_col_onthird">
        <div class="craftykits_user_cart">
            <div class="craftykits_user_cart_price">Total Price: <span class="cart_total">$<?php echo $product_price;?></span></div>
            <div class="craftykits_user_cart_price">Shipping: <span class="cart_shipping_total">$<?php echo $shipping_methods[0]['price'];?></span></div>
            <div class="craftykits_user_cart_price craftykits_user_cart_tprice">Grand Total: <span class="cart_grand_total">$<?php echo $total_price;?></span></div>
            <div class="craftykits_payment_button">
                <input type="hidden" name="craftykits_upload_id" id="craftykits_upload_id" value="<?php echo $attachement_id;?>" >
                <input type="hidden" name="craftykits_user_id" id="craftykits_user_id" value="<?php echo $user_id;?>" >
                <input type="hidden" name="craftykits_product_id" id="craftykits_product_id" value="<?php echo $product_id;?>" >
                <input type="hidden" name="craftykits_cart_orignal_grand_price" id="craftykits_cart_orignal_grand_price" value="" >
                <input type="hidden" name="craftykits_cart_grand_price" id="craftykits_cart_grand_price" value="<?php echo $total_price;?>" >
                <input type="hidden" name="craftykits_credit_card_id" id="craftykits_credit_card_id" value="" />
                
                <input type="hidden" name="craftykits_product_unit_price" id="craftykits_product_unit_price" value="" />
                <input type="hidden" name="craftykits_product_total_price" id="craftykits_product_total_price" value="" />
                <input type="hidden" name="craftykits_shipping_price" id="craftykits_shipping_price" value="" />
                <input type="hidden" name="craftykits_selected_product_quantity" id="craftykits_selected_product_quantity" value="" />
                
                <input type="hidden" name="craftykits_cc_form_action" id="craftykits_cc_form_action" value="<?php echo $step4_page;?>" />
                <input type="hidden" name="craftykits_paypal_form_action" id="craftykits_paypal_form_action" value="<?php echo $paypal_form_action;?>" />
                <input type="hidden" name="craftykits_paypal_form_submit" id="craftykits_paypal_form_submit" value="no" />
                
                <!-- Prepopulate the PayPal checkout page with customer details, -->
                <input type="hidden" name="first_name" value="<?php echo $user_meta['craftykits_user_billing_name'];?>">
                <input type="hidden" name="last_name" value="">
                <input type="hidden" name="email" value="<?php echo $user_info-> user_email ?>">
                <input type="hidden" name="address1" value="<?php echo $user_meta['craftykits_user_billing_address1'];?>">
                <input type="hidden" name="address2" value="<?php echo $user_meta['craftykits_user_billing_address2'];?>">
                <input type="hidden" name="city" value="<?php echo $user_meta['craftykits_user_billing_city'];?>">
                <input type="hidden" name="zip" value="<?php echo $user_meta['craftykits_user_billing_zip'];?>">
                
                <input type="hidden" name="cmd" value="_xclick" />
                <input type="hidden" name="business" value="<?php echo $paypal_business;?>" />
                <input type="hidden" name="currency_code" value="USD" />
                <input type="hidden" name="quantity" id="craftykits_paypal_quantity" value="1" />
                <input type="hidden" name="item_name" id="craftykits_paypal_name" value="" />
                <input type="hidden" name="amount" id="craftykits_paypal_price" value="<?php echo $total_price;?>" />
                <input type="hidden" name="custom" id="craftykits_paypal_custom" value="<?php echo $attachement_id;?>" />
                <input type="hidden" name="return" value="<?php echo $step4_page;?>?payment=paypal" />
                <input type="hidden" name="cancel_return" id="craftykits_paypal_cancel_return" value="<?php echo $step2_page;?>" />
                <!-- Where to send the PayPal IPN to. -->
                <input type="hidden" name="notify_url" value="<?php echo CRAFTYKITS_SITE_BASE_URL."/?craftykits_file_uplaod=yes&paypal=process&user=".$user_id; ?>" />
                
                <input type="submit" id="craftykits_cart_button" name="btnSubmit" class="btn btn-large btn-primary" value="Order Now" >       
                
                <div class="craftykits_upload_progress_area craftykits_payment_progress_area">
                    <div class="craftykits_file" id="uploadFile0">
                        <div class="craftykits_file_progress">
                            <span class="status uploading">Please Wait...</span>
                            <div class="craftykits_bar">
                                <div class="craftykits_progress"></div>                               
                            </div>                            
                        </div>                    
                    </div>
                </div>
            </div>
            <div class="craftykits_cart_terms">By clicking continue you agree to our <a href="<?php echo $terms_page_url;?>" target="_blank">terms & conditions</a></div>
        </div>
        <div class="craftykits_clear_both"></div>
    </div>
    </form>    
</div>
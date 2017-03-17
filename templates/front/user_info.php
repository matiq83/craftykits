<div class="craftykits_userinfo_container">
    <form name="frm_craftykits_userinfo" id="frm_craftykits_userinfo" action="<?php echo $step3_page;?>" method="post">
    <div class="craftykits_user_upload_heading">Your Upload</div>
    <div class="craftykits_userinfo_first_row craftykits_userinfo_row">
        <div class="craftykits_user_upload">
            <?php echo $craftykits_upload_img;?>
        </div>
        <div class="craftykits_user_cart_area">
            <div class="craftykits_user_name">Hi <span><?php echo $user_name;?></span></div>
            <div class="craftykits_user_cart">
                <div class="craftykits_user_cart_quantity">Quantity: 1</div>
                <div class="craftykits_user_cart_price">Price: <?php echo $default_price;?></div>
                <div class="craftykits_payment_button">
                    <input type="hidden" name="craftykits_upload_id" id="craftykits_upload_id" value="<?php echo $attachement_id;?>" >
                    <input type="hidden" name="craftykits_user_id" id="craftykits_user_id" value="<?php echo $current_user->ID;?>" >
                    <input type="submit" id="craftykits_cart_button" name="btnSubmit" class="btn btn-large btn-primary" value="Continue to Payment" >                
                </div>
                <div class="craftykits_cart_terms">By clicking continue you agree to our <a href="<?php echo $terms_page_url;?>" target="_blank">terms & conditions</a></div>
            </div>
        </div>
    </div>
    <div class="craftykits_clear_both"></div>
    <?php if ( 0 == $current_user->ID ) { ?>
    <div class="craftykits_userinfo_second_row craftykits_userinfo_row">
        <div class="craftykits_user_login_box craftykits_fileds_box">
            <div class="box_title"><span>User Basic Information</span></div>
            <div class="box_content">
                <div class="craftykits_user_register_area">
                    <div class="box_field">
                        <label>Email</label>
                        <input type="email" name="craftykits_user_email" id="craftykits_user_email" value="" data-validation-engine="validate[required,custom[email],ajax[validate_register_user_email]]">
                    </div>
                    <div class="box_field">
                        <label>Username</label>
                        <input type="text" name="craftykits_register_user_name" id="craftykits_register_user_name" value="" data-validation-engine="validate[required,custom[onlyLetterNumber],ajax[validate_register_username]]">
                    </div>
                    <div class="box_field">
                        <label>Password</label>
                        <input type="password" name="craftykits_register_user_password" id="craftykits_register_user_password" value="" data-validation-engine="validate[required]">
                    </div>
                    <div class="box_field">
                        <label>Confirm Password</label>
                        <input type="password" name="craftykits_register_user_password2" id="craftykits_register_user_password2" value="" data-validation-engine="validate[required,equals[craftykits_register_user_password]]">
                    </div>
                </div>
                <div class="craftykits_user_area_or">OR</div>                
                <div class="craftykits_user_login_area">
                    <div class="box_field">
                        <label>Username</label>
                        <input type="text" name="craftykits_login_user_name" id="craftykits_login_user_name" value=""  onblur="javascript: craftykits_validate_login_form();">
                    </div>
                    <div class="box_field">
                        <label>Password</label>
                        <input type="password" name="craftykits_login_user_password" id="craftykits_login_user_password" value="" onblur="javascript: craftykits_validate_login_form();">
                    </div>
                    <div class="box_field">
                        <label></label>
                        <input class="btn" onclick="javascript: craftykits_submit_login_form();" type="button" name="craftykits_btn_login" id="craftykits_btn_login" value="Login & Load Billing/Shipping Details">
                        <img src="<?php echo CRAFTYKITS_PLUGIN_URL;?>/images/ajax-loader.gif" id="craftykits_ajax_img" >
                    </div>
                </div>
                
                <div class="craftykits_clear_both"></div>
            </div>
        </div>       
    </div>
    <?php }?>
    <div class="craftykits_clear_both"></div>
    
    <div class="craftykits_userinfo_second_row craftykits_userinfo_row">
        <div class="craftykits_user_billing_box craftykits_fileds_box">
            <div class="box_title"><span>Billing Address</span></div>
            <div class="box_content">
                <div class="box_field">
                    <label>Name</label>
                    <input type="text" name="craftykits_user_billing_name" id="craftykits_user_billing_name" value="<?php echo $user_meta['craftykits_user_billing_name'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>Address1</label>
                    <input type="text" name="craftykits_user_billing_address1" id="craftykits_user_billing_address1" value="<?php echo $user_meta['craftykits_user_billing_address1'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>Address2</label>
                    <input type="text" name="craftykits_user_billing_address2" id="craftykits_user_billing_address2" value="<?php echo $user_meta['craftykits_user_billing_address2'];?>">
                </div>
                <div class="box_field">
                    <label>City</label>
                    <input type="text" name="craftykits_user_billing_city" id="craftykits_user_billing_city" value="<?php echo $user_meta['craftykits_user_billing_city'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>State/Provence/Region</label>
                    <input type="text" name="craftykits_user_billing_state" id="craftykits_user_billing_state" value="<?php echo $user_meta['craftykits_user_billing_state'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>Zip/Postal Code</label>
                    <input type="text" name="craftykits_user_billing_zip" id="craftykits_user_billing_zip" value="<?php echo $user_meta['craftykits_user_billing_zip'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>Country</label>
                    <select name="craftykits_user_billing_country" id="craftykits_user_billing_country" data-validation-engine="validate[required]">
                        <option value="">Select Country</option>
                        <?php echo $billing_countries;?>
                    </select>
                </div>
            </div>
        </div>
        <div class="craftykits_user_shipping_box craftykits_fileds_box">
            <div class="box_title"><span>Shipping Address</span><span><input type="checkbox" name="craftykits_shipping_same" id="craftykits_shipping_same" value="1">Same as Billing?</span>
            <div class="craftykits_clear_both_no_margin"></div>
            </div>
            <div class="box_content">
                <div class="box_field">
                    <label>Name</label>
                    <input type="text" name="craftykits_user_shipping_name" id="craftykits_user_shipping_name" value="<?php echo $user_meta['craftykits_user_shipping_name'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>Address1</label>
                    <input type="text" name="craftykits_user_shipping_address1" id="craftykits_user_shipping_address1" value="<?php echo $user_meta['craftykits_user_shipping_address1'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>Address2</label>
                    <input type="text" name="craftykits_user_shipping_address2" id="craftykits_user_shipping_address2" value="<?php echo $user_meta['craftykits_user_shipping_address2'];?>">
                </div>
                <div class="box_field">
                    <label>City</label>
                    <input type="text" name="craftykits_user_shipping_city" id="craftykits_user_shipping_city" value="<?php echo $user_meta['craftykits_user_shipping_city'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>State/Provence/Region</label>
                    <input type="text" name="craftykits_user_shipping_state" id="craftykits_user_shipping_state" value="<?php echo $user_meta['craftykits_user_shipping_state'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>Zip/Postal Code</label>
                    <input type="text" name="craftykits_user_shipping_zip" id="craftykits_user_shipping_zip" value="<?php echo $user_meta['craftykits_user_shipping_zip'];?>" data-validation-engine="validate[required]">
                </div>
                <div class="box_field">
                    <label>Country</label>
                    <select name="craftykits_user_shipping_country" id="craftykits_user_shipping_country" data-validation-engine="validate[required]" >
                        <option value="">Select Country</option>
                        <?php echo $shipping_countries;?>
                    </select>
                    <span class="craftykits_shipping_note"><i>Note:</i> We can not ship to PO Boxes</span>
                </div>
            </div>
        </div>
    </div>
    <div class="craftykits_clear_both"></div>
    </form>
</div>
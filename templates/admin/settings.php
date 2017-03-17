<?php if ( $message!="") : ?>
<div id="message" class="updated fade"><p><strong><?php echo $message; ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php echo CRAFTYKITS_PLUGIN_NAME;?> Settings</h2>
<table class="wp-list-table widefat fixed" cellspacing="0">
	<thead>
        <tr>
            <th scope="col" class="manage-column" style=""><?php echo CRAFTYKITS_PLUGIN_NAME;?> Settings</th>
        </tr>
	</thead>
	<tbody id="the-list">
        <tr>
            <td>
            	<form method="post" name="frm_craftykits" id="frm_craftykits" class="frm_craftykits" action="edit.php?post_type=craftykits_product&page=craftykits_settings" enctype="multipart/form-data">
                <table width="100%">
                    <tr>
                    	<td width="180">Default Product</td>
                        <td>
                            <select name="craftykits_default_product" id="craftykits_default_product">
                                <?php foreach( $products as $product ) { ?>
                                    <?php if( $options['craftykits_default_product'] == $product->ID ) {?>
                                    <option value="<?php echo $product->ID;?>" selected><?php echo $product->post_title;?></option>
                                    <?php }else{?>
                                    <option value="<?php echo $product->ID;?>"><?php echo $product->post_title;?></option>
                                    <?php }?>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td width="180">Terms and Conditions Page</td>
                        <td>
                            <select name="craftykits_terms_page" id="craftykits_terms_page">
                                <?php foreach( $pages as $page ) { ?>
                                    <?php if( $options['craftykits_terms_page'] == $page->ID ) {?>
                                    <option value="<?php echo $page->ID;?>" selected><?php echo $page->post_title;?></option>
                                    <?php }else{?>
                                    <option value="<?php echo $page->ID;?>"><?php echo $page->post_title;?></option>
                                    <?php }?>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                    	<td width="180">PayPal Business Email</td>
                        <td>
                            <input type="text" name="craftykits_paypal_business" id="craftykits_paypal_business" value="<?php echo $options['craftykits_paypal_business'];?>" />
                        </td>
                    </tr>
                    <tr>
                    	<td width="180">PayPal Rest API ClientId</td>
                        <td>
                            <input type="text" name="craftykits_paypal_client_id" id="craftykits_paypal_client_id" value="<?php echo $options['craftykits_paypal_client_id'];?>" />
                        </td>
                    </tr>
                    <tr>
                    	<td>PayPal Rest API Secret Key</td>
                        <td>
                            <input type="text" name="craftykits_paypal_secret_key" id="craftykits_paypal_secret_key" value="<?php echo $options['craftykits_paypal_secret_key'];?>" class="textfield" />
                        </td>
                    </tr>
                    <tr>
                    	<td width="180">PayPal Test Mode</td>
                        <td>
                            <input type="radio" name="craftykits_paypal_test_mode" value="yes" id="craftykits_paypal_test_mode_yes"<?php if( $options['craftykits_paypal_test_mode'] == 'yes' || $options['craftykits_paypal_test_mode'] == '' ) { echo ' checked';}?> />Yes  
                            <input type="radio" name="craftykits_paypal_test_mode" value="no" id="craftykits_paypal_test_mode_no"<?php if( $options['craftykits_paypal_test_mode'] == 'no' ) { echo ' checked';}?> />No 
                        </td>
                    </tr>
                    <tr>
                    	<td>Order Created Customer Notification Email Subject</td>
                        <td>
                            <input type="text" name="craftykits_customer_order_email_subject" id="craftykits_customer_order_email_subject" value="<?php echo $options['craftykits_customer_order_email_subject'];?>" class="textfield" />
                            <br><strong>Available Tags:</strong> <i>[siteurl], [buyername], [buyeremail], [order_id]</i>
                        </td>
                    </tr>
                    <tr>
                    	<td>Order Created Customer Notification Email Body Template
                            <br><br><strong>Available Tags</strong><br><i>[siteurl], [buyername], [buyeremail], [order_id], [order_detail]</i>
                        </td>
                        <td>
                            <?php wp_editor( $options['craftykits_customer_order_email_body'], 'craftykits_customer_order_email_body' );?>                            
                        </td>
                    </tr>
                    <tr>
                    	<td>Order Created Admin Notification Email Subject</td>
                        <td>
                            <input type="text" name="craftykits_admin_order_email_subject" id="craftykits_admin_order_email_subject" value="<?php echo $options['craftykits_admin_order_email_subject'];?>" class="textfield" />
                            <br><strong>Available Tags:</strong> <i>[siteurl], [buyername], [order_id]</i>
                        </td>
                    </tr>
                    <tr>
                    	<td>Order Created Admin Notification Email Body Template
                            <br><br><strong>Available Tags</strong><br><i>[siteurl], [buyername], [buyeremail], [order_id], [order_detail]</i>
                        </td>
                        <td>
                            <?php wp_editor( $options['craftykits_admin_order_email_body'], 'craftykits_admin_order_email_body' );?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="btnsave" id="btnsave" value="Update" class="button button-primary">
                        </td>
                    </tr>
                </table>
                </form>
            </td>
        </tr>
     </tbody>
</table>
</div>
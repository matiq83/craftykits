jQuery(document).ready( function( $ ) {
    if( $('.craftykits_product_cart_img').length ) {
        $('.craftykits_product_cart_img').fancybox({
            openEffect	: 'elastic',
            closeEffect	: 'elastic',
            helpers : {
                    title : {
                            type : 'over'
                    }
            }
        });
    }
    
    if($('.craftykits_upload_photo').length ) {
        $('.craftykits_upload_photo').dmUploader({
        url: craftykits_file_uplaod,
        dataType: 'json',
        allowedTypes: 'image/*',
        /*extFilter: 'jpg;png;gif',*/
        onInit: function(){
            
        },
        onBeforeUpload: function(id){
            craftykits_update_file_upload_status(id, 'uploading', 'Uploading...');
        },
        onNewFile: function(id, file){
            craftykits_add_file_to_upload(id, file);
        },
        onComplete: function(){
            //move to next url
        },
        onUploadProgress: function(id, percent){
            if( percent > 0 ) {
                percent = percent+11;
            }
            var percentStr = percent + '%';

            craftykits_update_file_upload_progress(id, percentStr);
        },
        onUploadSuccess: function(id, data){
            craftykits_update_file_upload_status(id, 'success', 'Upload Complete');
            craftykits_update_file_upload_progress(id, '111%');
            window.location.href = craftykits_file_uplaod_step2+'hash='+data['hash'];
        },
        onUploadError: function(id, message){
            craftykits_update_file_upload_status(id, 'error', message);
            craftykits_on_error_show_upload();
        },
        onFileTypeError: function(file){
            alert('File \'' + file.name + '\' cannot be added: must be an image'); 
            craftykits_on_error_show_upload();
        },
        onFileSizeError: function(file){
            alert('File \'' + file.name + '\' cannot be added: size excess limit');
            craftykits_on_error_show_upload();
        },
        /*onFileExtError: function(file){
          $.danidemo.addLog('#demo-debug', 'error', 'File \'' + file.name + '\' has a Not Allowed Extension');
        },*/
        onFallbackMode: function(message){
            alert('Browser not supported(do something else here!): ' + message);
            craftykits_on_error_show_upload();
        }
      });
    }
    
    if( $("#frm_craftykits_userinfo").length ) {
        $("#frm_craftykits_userinfo").validationEngine('attach', {
                                        onValidationComplete: function(form, status){
                                                                          var form_submit = jQuery( "#craftykits_paypal_form_submit" ).val();
                                                                          if( form_submit == 'yes' ) {
                                                                              return status;
                                                                          }
                                                                          if( $('#craftykits_cc_number').length ) {
                                                                              var paypal_option = $("#craftykits_payment_paypal").is(':checked');
                                                                              if( status ) {
                                                                                  cc_type = creditCardTypeFromNumber(jQuery('#craftykits_cc_number').val());
                                                                                  if( !paypal_option ) {
                                                                                    if( cc_type == 'UNKNOWN' ) {
                                                                                        jQuery('#craftykits_cc_number').validationEngine('showPrompt', 'Only Following Cards Are Acceptable: Visa, MasterCard, AmEx, Discover, Maestro', 'error' ,'topRight', true);
                                                                                        jQuery( "#craftykits_cart_button" ).attr( 'disabled', false );
                                                                                        jQuery( "#btn_coupon" ).attr( 'disabled', false );
                                                                                        jQuery( "#craftykits_cart_button" ).val( 'Order Now' );
                                                                                        jQuery( ".craftykits_payment_progress_area").hide();
                                                                                        return false;
                                                                                    }
                                                                                    var action = 'craftykits_process_payment';
                                                                                  }else{
                                                                                    var action = 'craftykits_process_paypal_payment';  
                                                                                  }
                                                                                  jQuery( "#craftykits_cart_button" ).val( 'Processing....' );
                                                                                  jQuery( "#craftykits_cart_button" ).attr( 'disabled', true );
                                                                                  jQuery( "#btn_coupon" ).attr( 'disabled', true );
                                                                                  var payment_progress_counter = 5;
                                                                                  jQuery(".craftykits_progress").css( 'width', payment_progress_counter+'%' ); 
                                                                                  jQuery( ".craftykits_payment_progress_area").show();
                                                                                  var payment_progress = setInterval(function(){ 
                                                                                      var increment = Math.floor((Math.random() * 5) + 3);
                                                                                      payment_progress_counter = payment_progress_counter+increment; 
                                                                                      if( payment_progress_counter >= 100 ) {
                                                                                          clearInterval( payment_progress );
                                                                                      }
                                                                                      jQuery(".craftykits_progress").css( 'width', payment_progress_counter+'%' ); 
                                                                                  }, 1000);
                                                                                  
                                                                                  jQuery.ajax({
                                                                                                type: 'POST',
                                                                                                dataType: 'json',
                                                                                                url: craftykits_file_uplaod,
                                                                                                data: { 
                                                                                                        'action': action,
                                                                                                        'cc': jQuery('#craftykits_cc_number').val(), 
                                                                                                        'cvv': jQuery("#craftykits_cc_number_verification").val(),
                                                                                                        'cc_exp_month': jQuery("#craftykits_cc_exp_month").val(),
                                                                                                        'cc_exp_year': jQuery("#craftykits_cc_exp_year").val(),
                                                                                                        'craftykits_upload_id': jQuery("#craftykits_upload_id").val(),
                                                                                                        'craftykits_user_id': jQuery("#craftykits_user_id").val(),
                                                                                                        'craftykits_product_id': jQuery("#craftykits_product_id").val(),
                                                                                                        'craftykits_cart_grand_price': jQuery("#craftykits_cart_grand_price").val(),
                                                                                                        'cc_type' : cc_type,
                                                                                                        'customer_notes' : jQuery("#craftykits_customer_notes").val(),
                                                                                                        'product_price' : jQuery("#craftykits_product_unit_price").val(),
                                                                                                        'cart_total' :jQuery("#craftykits_product_total_price").val(),
                                                                                                        'shipping_price' : jQuery("#craftykits_shipping_price").val(),
                                                                                                        'product_quantity' : jQuery("#craftykits_selected_product_quantity").val()
                                                                                                      },
                                                                                                success: function(data){
                                                                                                    if ( data.valid_cc == false ){
                                                                                                        jQuery('#craftykits_cc_number').validationEngine('showPrompt', data.message, 'error' ,'topRight', true);
                                                                                                        jQuery( "#craftykits_cart_button" ).attr( 'disabled', false );
                                                                                                        jQuery( "#btn_coupon" ).attr( 'disabled', false );
                                                                                                        jQuery( "#craftykits_cart_button" ).show( 'Order Now' );
                                                                                                        jQuery( ".craftykits_payment_progress_area").hide();
                                                                                                        return false;                                       
                                                                                                    }else{
                                                                                                        if( !paypal_option ) {
                                                                                                            window.location.replace( $("#frm_craftykits_userinfo").attr('action')+"?order_id="+data.order_id );
                                                                                                        }else{
                                                                                                            jQuery( "#craftykits_paypal_custom").val( data.order_id );
                                                                                                            var paypal_cancel = jQuery( "#craftykits_paypal_cancel_return" ).val();
                                                                                                            jQuery( "#craftykits_paypal_cancel_return" ).val( paypal_cancel+"&paypal_cancel_return=true&order_id="+data.order_id );
                                                                                                            jQuery( "#craftykits_paypal_form_submit" ).val( 'yes' );
                                                                                                            $("#frm_craftykits_userinfo").submit();
                                                                                                        }                                                                                                        
                                                                                                    }
                                                                                                    
                                                                                                }
                                                                                            });
                                                                              }else{
                                                                                  return status;
                                                                              }
                                                                          }else{
                                                                              return status;
                                                                          }                                                                          
                                                                      }
        });
    }
    
    if( $("#craftykits_shipping_same").length ) {
        $("#craftykits_shipping_same").click(function(){
            if( $(this).is(':checked') ) {
                $('#craftykits_user_shipping_name').val($('#craftykits_user_billing_name').val());
                $('#craftykits_user_shipping_address1').val($('#craftykits_user_billing_address1').val());
                $('#craftykits_user_shipping_address2').val($('#craftykits_user_billing_address2').val());
                $('#craftykits_user_shipping_city').val($('#craftykits_user_billing_city').val());
                $('#craftykits_user_shipping_state').val($('#craftykits_user_billing_state').val());
                $('#craftykits_user_shipping_zip').val($('#craftykits_user_billing_zip').val());
                $('#craftykits_user_shipping_country').val($('#craftykits_user_billing_country').val());
            }else{
                $('#craftykits_user_shipping_name').val('');
                $('#craftykits_user_shipping_address1').val('');
                $('#craftykits_user_shipping_address2').val('');
                $('#craftykits_user_shipping_city').val('');
                $('#craftykits_user_shipping_state').val('');
                $('#craftykits_user_shipping_zip').val('');
                $('#craftykits_user_shipping_country').val('');
            }
            $('#craftykits_user_shipping_name').trigger('blur');
            $('#craftykits_user_shipping_address1').trigger('blur');
            $('#craftykits_user_shipping_address2').trigger('blur');
            $('#craftykits_user_shipping_city').trigger('blur');
            $('#craftykits_user_shipping_state').trigger('blur');
            $('#craftykits_user_shipping_zip').trigger('blur');
            $('#craftykits_user_shipping_country').trigger('blur');
        });
    }
    
    if( $(".craftykits_payment_options").length ) {
        $("#craftykits_payment_cc").click( function() {
            $( ".craftykits_user_payment_box .box_content" ).slideDown( 'slow' );
            $("#frm_craftykits_userinfo").attr( 'action', $("#craftykits_cc_form_action").val() );
        });
        
        $("#craftykits_payment_paypal").click( function() {
            $( ".craftykits_user_payment_box .box_content" ).slideUp( 'slow' );
            $("#frm_craftykits_userinfo").attr( 'action', $("#craftykits_paypal_form_action").val() );
            $("#craftykits_paypal_quantity").val( $("#craftykits_selected_product_quantity").val() );
            $("#craftykits_paypal_price").val( $("#craftykits_cart_grand_price").val() );
            $("#craftykits_paypal_name").val( $( "#craftykits_product option:selected" ).text() );
        });
    }
    
    if( $("#craftykits_product").length ) {
        $("#craftykits_product").change( function(){
            craftykits_update_cart_total();
        });
    }
    if( $("#craftykits_product_quantity").length ) {
        $("#craftykits_product_quantity").change( function(){
            craftykits_update_cart_total();
        });
    }
    if( $("input[type='radio'][name='craftykits_shipping']").length ) {
        $("input[type='radio'][name='craftykits_shipping']").click( function(){
            craftykits_update_cart_total();
        });
    }
    if( $('#craftykits_payment_cc').length ) {
        craftykits_update_cart_total();
        jQuery("#craftykits_payment_cc").trigger('click');
    }
});

function craftykits_update_cart_total() {
    product = jQuery("#craftykits_product").val();
    product_arr = product.split( "@@" );
    product_price = product_arr[1];
    product_id = product_arr[0];
    
    product_quantity = jQuery("#craftykits_product_quantity").val();
    shipping_price = 1*(jQuery("input[type='radio'][name='craftykits_shipping']:checked").val());
    cart_total = product_price*product_quantity;
    grand_price = cart_total+shipping_price;
    
    grand_price = Math.round(grand_price * 100) / 100;
    cart_total = Math.round(cart_total * 100) / 100;
    shipping_price = Math.round(shipping_price * 100) / 100;
    
    jQuery('.craftykits_user_cart_unit_price').html( '$'+product_price );
    jQuery('.craftykits_user_cart span.cart_total').html( '$'+cart_total );
    jQuery('.craftykits_user_cart span.cart_shipping_total').html( '$'+shipping_price );
    jQuery('.craftykits_user_cart span.cart_grand_total').html( '$'+grand_price );
    jQuery("#craftykits_cart_grand_price").val(grand_price);
    jQuery("#craftykits_product_id").val(product_id);
    jQuery("#craftykits_cart_orignal_grand_price").val("");
    jQuery( "#craftykits_cart_button" ).attr( 'disabled', false );
    jQuery( "#btn_coupon" ).attr( 'disabled', false );
    jQuery( "#craftykits_cart_button" ).val( 'Order Now' );
    
    jQuery("#craftykits_product_unit_price").val(product_price);
    jQuery("#craftykits_product_total_price").val(cart_total);
    jQuery("#craftykits_shipping_price").val(shipping_price);
    jQuery("#craftykits_selected_product_quantity").val(product_quantity);
    
    jQuery("#craftykits_paypal_quantity").val( jQuery("#craftykits_selected_product_quantity").val() );
    jQuery("#craftykits_paypal_price").val( jQuery("#craftykits_cart_grand_price").val() );
    jQuery("#craftykits_paypal_name").val( jQuery( "#craftykits_product option:selected" ).text() );
    /*
    var upload_id = jQuery("#craftykits_upload_id").val();
    var product_id = jQuery("#craftykits_product_id").val();
    var user_id = jQuery("#craftykits_user_id").val();
    jQuery( "#craftykits_paypal_custom").val( upload_id+"@"+product_id+"@"+user_id+"@"+shipping_price );
    */
}

function craftykits_validate_coupon() {
    var error = false;
    if( jQuery("#craftykits_coupons").val() == "" ) {
        error = true;
        jQuery('#craftykits_coupons').validationEngine('showPrompt', 'Please provide coupon code!', 'error' ,'topRight', true);
    }
    return error;
}

function craftykits_submit_coupon() {
    
    if( !craftykits_validate_coupon() ) {
        if( jQuery("#craftykits_cart_orignal_grand_price").val() == "" ) {
            jQuery("#craftykits_cart_orignal_grand_price").val(jQuery("#craftykits_cart_grand_price").val());
        }
        jQuery('#craftykits_coupons').validationEngine('hide');
        jQuery( "#craftykits_ajax_img" ).show();
        jQuery( "#craftykits_cart_button" ).attr( 'disabled', true );
        jQuery( "#btn_coupon" ).attr( 'disabled', true );
        
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: craftykits_file_uplaod,
            data: { 
                    'action': 'apply_coupon',
                    'coupon': jQuery('#craftykits_coupons').val(), 
                    'product_id': jQuery("#craftykits_product_id").val(),
                    'grand_price': jQuery("#craftykits_cart_orignal_grand_price").val()
                  },
            success: function(data){
                if ( data.valid_coupon == false ){
                    jQuery('#craftykits_coupons').validationEngine('showPrompt', data.message, 'error' ,'topRight', true);
                }else{
                    jQuery("#craftykits_cart_grand_price").val(data.price);
                    jQuery('.craftykits_user_cart span.cart_grand_total').html( data.price_html );
                    jQuery("#craftykits_cart_orignal_grand_price").val(data.orignal_grand_price);
                    jQuery("#craftykits_paypal_price").val( jQuery("#craftykits_cart_grand_price").val() );                    
                }
                jQuery( "#craftykits_ajax_img" ).hide();
                jQuery( "#craftykits_cart_button" ).attr( 'disabled', false );
                jQuery( "#btn_coupon" ).attr( 'disabled', false );
            }
        });
    }
}

function craftykits_validate_login_form() {
    
    var error = false;
    
    if( jQuery('#craftykits_login_user_name').val() == "" ) {
        error = true;
        jQuery('#craftykits_login_user_name').validationEngine('showPrompt', 'This field is required', 'error' ,'topRight', true);
    }
    if( jQuery('#craftykits_login_user_password').val() == "" ) {
        error = true;
        jQuery('#craftykits_login_user_password').validationEngine('showPrompt', 'This field is required', 'error', 'topRight', true);
    }
    
    return error;
}

function craftykits_submit_login_form() {
    
    if( !craftykits_validate_login_form() ) {
        jQuery('#craftykits_login_user_name').validationEngine('hide');
        jQuery('#craftykits_login_user_password').validationEngine('hide');
        jQuery( "#craftykits_ajax_img" ).show();
        jQuery( "#craftykits_cart_button" ).attr( 'disabled', true );
        jQuery( "#craftykits_btn_login" ).attr( 'disabled', true );
        
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: craftykits_file_uplaod,
            data: { 
                    'action': 'ajaxlogin',
                    'username': jQuery('#craftykits_login_user_name').val(), 
                    'password': jQuery('#craftykits_login_user_password').val() 
                  },
            success: function(data){
                if ( data.loggedin == false ){
                    alert(data.message);
                    jQuery( "#craftykits_ajax_img" ).hide();
                    jQuery( "#craftykits_cart_button" ).attr( 'disabled', false );
                    jQuery( "#craftykits_btn_login" ).attr( 'disabled', false );
                }else{
                    jQuery( "#craftykits_cart_button" ).attr( 'disabled', false );
                    location.reload();
                }                
            }
        });
    }
}

function craftykits_update_file_upload_progress( id, percent ) {
    
    jQuery('#uploadFile' + id).find('div.craftykits_progress').width(percent);
}

function craftykits_update_file_upload_status( id, status, message ) {
    jQuery('#uploadFile' + id).find('span.status').html(message).addClass(status);
}

function craftykits_add_file_to_upload( id, file ) {
    var template = '' +
          '<div class="craftykits_file" id="uploadFile' + id + '">' +
            '<div class="craftykits_file_progress">' +
            '<span class="status">Waiting</span>' +
            '<div class="craftykits_bar">' +
              '<div class="craftykits_progress" style="width:0%"></div>' +
            '</div>' +
            '</div>' +
            '<div class="info">' +
              '<span class="filename" title="Size: ' + file.size + 'bytes - Mimetype: ' + file.type + '">' + craftykits_truncate(file.name, 14) + '</span>' +
            '</div>' +
          '</div>';
          
    jQuery('.craftykits_upload_progress_area').html(template);
    jQuery('.craftykits_upload_button_area').hide();
    jQuery('.craftykits_upload_progress_area').show();
}

function craftykits_on_error_show_upload() {
    setTimeout( function(){
                jQuery('.craftykits_upload_button_area').show();
                jQuery('.craftykits_upload_progress_area').hide();
            }, 3000 );
}

function craftykits_truncate(n, len) {
    var ext = n.substring(n.lastIndexOf(".") + 1, n.length).toLowerCase();
    var filename = n.replace('.' + ext,'');
    if(filename.length <= len) {
        return n;
    }
    filename = filename.substr(0, len) + (n.length > len ? '[...]' : '');
    return filename + '.' + ext;
}

function creditCardTypeFromNumber(num) {
    // first, sanitize the number by removing all non-digit characters.
    num = num.replace(/[^\d]/g,'');
    // now test the number against some regexes to figure out the card type.
    if (num.match(/^5[1-5]\d{14}$/)) {
        return 'mastercard';
    } else if (num.match(/^4\d{15}/) || num.match(/^4\d{12}/)) {
        return 'visa';
    } else if (num.match(/^3[47]\d{13}/)) {
        return 'amex';
    } else if (num.match(/^6011\d{12}/)) {
        return 'discover';
    } else if(num.match(/^(5018|5020|5038|6304|6759|676[1-3])/)) {
        return 'maestro';
    }else if(num.match(/^35(2[89]|[3-8][0-9])/)) {
        return 'jcb';
    }else if(num.match(/^36/)) {
        return 'diners_club_international';
    }else if(num.match(/^30[0-5]/)) {
        return 'diners_club_carte_blanche';
    }
    return 'UNKNOWN';
}
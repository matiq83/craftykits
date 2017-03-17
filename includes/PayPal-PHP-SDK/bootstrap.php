<?php
require __DIR__ . '/autoload.php';;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

$clientId = "";
$clientSecret = "";
$mode = "";

if( function_exists('get_option') ) {
    $craftykits_settings = get_option( 'craftykits_settings' );
    $clientId = $craftykits_settings['craftykits_paypal_client_id'];
    $clientSecret = $craftykits_settings['craftykits_paypal_secret_key'];
    $mode = $craftykits_settings['craftykits_paypal_test_mode'];
    if( $mode == 'yes' ) {
        $mode = 'sandbox';
    }else{
        $mode = 'live';
    }
}

// Replace these values by entering your own ClientId and Secret by visiting https://developer.paypal.com/webapps/developer/applications/myapps
if( empty($clientId) ) {
    $clientId = 'AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS';
}
if( empty($clientSecret) ) {
    $clientSecret = 'EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL';
}
if( empty($mode) ) {
    $mode = 'sandbox';
}

/** @var \Paypal\Rest\ApiContext $apiContext */
$apiContext = getApiContext( $clientId, $clientSecret, $mode );

return $apiContext;
/**
 * Helper method for getting an APIContext for all calls
 * @param string $clientId Client ID
 * @param string $clientSecret Client Secret
 * @return PayPal\Rest\ApiContext
 */
function getApiContext($clientId, $clientSecret, $mode = 'sandbox' )
{

    // #### SDK configuration
    // Register the sdk_config.ini file in current directory
    // as the configuration source.
    /*
    if(!defined("PP_CONFIG_PATH")) {
        define("PP_CONFIG_PATH", __DIR__);
    }
    */


    // ### Api context
    // Use an ApiContext object to authenticate
    // API calls. The clientId and clientSecret for the
    // OAuthTokenCredential class can be retrieved from
    // developer.paypal.com

    $apiContext = new ApiContext(
        new OAuthTokenCredential(
            $clientId,
            $clientSecret
        )
    );

    // Comment this line out and uncomment the PP_CONFIG_PATH
    // 'define' block if you want to use static file
    // based configuration

    $apiContext->setConfig(
        array(
            'mode' => $mode,
            'log.LogEnabled' => true,
            'log.FileName' => '../PayPal.log',
            'log.LogLevel' => 'DEBUG', // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
            'validation.level' => 'log',
            'cache.enabled' => true,
            // 'http.CURLOPT_CONNECTTIMEOUT' => 30
            // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
        )
    );

    // Partner Attribution Id
    // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
    // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
    // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');

    return $apiContext;
}
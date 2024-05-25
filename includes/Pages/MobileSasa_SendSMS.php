<?php
/**
 * The file that defines the Mobile Sasa Send SMS class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/Pages
 *
 * @author Wilson Devops <wilsonkabatha@gmail.com>
*/

namespace Wylly\MobileSasa\Pages;

// If direct access, then exit the file.
defined( 'ABSPATH' ) || exit;

if( ! class_exists('MobileSasa_SendSMS')){

    class MobileSasa_SendSMS {
        
        private static $wc_senderid;
        private static $wc_apitoken;

        public static function init( $senderid, $apitoken ) {
            self::$wc_senderid = $senderid;
            self::$wc_apitoken = $apitoken;
        }

        // Send SMS
        public static function wcSendExpressPostSMS( $phones, $message ): int {
            $status = 0;
            $multiple_numbers = strpos( $phones, ',' ) !== false;

            $url = $multiple_numbers ? 'https://api.mobilesasa.com/v1/send/bulk' : 'https://api.mobilesasa.com/v1/send/message';
            $phone_param = $multiple_numbers ? 'phones' : 'phone';

            $postData = [
                'senderID' => self::$wc_senderid,
                'message'  => $message,
                $phone_param => $phones,
                'api_token' => self::$wc_apitoken
            ];

            $curl = curl_init();
            curl_setopt_array( $curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_POST => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 400,
                CURLOPT_POSTFIELDS => http_build_query( $postData )
            ]);

            $response = curl_exec( $curl );

            if ($response === false) {
                // Handle curl error
                $error = curl_error( $curl );
                error_log( 'cURL Error: ' . $error );
            }

            curl_close( $curl );

            if ($response) {
                $responseVals = json_decode( $response, true );
                if ( isset($responseVals['responseCode']) && $responseVals['responseCode'] == '0200' ) {
                    $status = 1;
                }
            }

            return $status;
        }

        // Clean phone numbers
        public static function wcCleanPhone( $phones ): string {
            $cleaned_phones = [];
            $phones_array = explode( ",", $phones );

            foreach ( $phones_array as $phone ) {
                $cleaned_phone = "254" . substr( preg_replace( '/[^0-9]/', '', $phone ), -9 );
                $cleaned_phones[] = $cleaned_phone;
            }

            return implode( ",", $cleaned_phones );
        }
    }
    
}
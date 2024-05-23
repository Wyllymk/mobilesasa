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

// if direct access than exit the file.
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
        public static function wc_sendExpressPostSMS( $phones, $message ): int {
            $status = 0;
            $multiple_numbers = strpos( $phones, ',' );

            $url = $multiple_numbers !== false ? 'https://api.mobilesasa.com/v1/send/bulk' : 'https://api.mobilesasa.com/v1/send/message';
            $phone_param = $multiple_numbers !== false ? 'phones' : 'phone';

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
            curl_close( $curl );

            $responseVals = json_decode( $response, true );
            if ( $responseVals['responseCode'] == '0200' ) {
                $status = 1;
            }

            return $status;
        }

        // Clean phone numbers
        public static function wc_clean_phone( $phones ): string {
            $cleaned_phones = [];
            $phones_array = explode( ",", $phones );

            foreach ( $phones_array as $phone ) {
                $tel = str_replace( [' ', '<', '>', '&', '{', '}', '*', "+", '!', '@', '#', "$", '%', '^', '&'], "", str_replace( "-", "", $phone ) );
                $cleaned_phone = "254" . substr( $tel, -9 );
                $cleaned_phones[] = $cleaned_phone;
            }

            return implode( ",", $cleaned_phones );
        }
    }
    
}
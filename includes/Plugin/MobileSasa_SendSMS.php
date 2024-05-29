<?php
/**
 * The file that defines the Mobile Sasa Send SMS class
 *
 * @link http://wilsondevops.com
 * @since 1.0.0
 *
 * @package MobileSasa
 * @subpackage MobileSasa/includes/Plugin
 *
 * @author Wilson Devops <wilsonkabatha@gmail.com>
*/
namespace Wylly\MobileSasa\Plugin;

// If direct access, then exit the file.
defined('ABSPATH') || exit;

if (!class_exists('MobileSasa_SendSMS')) {

    class MobileSasa_SendSMS {

        private static $wc_senderid;
        private static $wc_apitoken;

        /**
         * Initialize the class with the sender ID and API token.
         *
         * @param string $senderid The sender ID for the SMS service.
         * @param string $apitoken The API token for the SMS service.
         */
        public static function init(string $senderid, string $apitoken): void {
            self::$wc_senderid = $senderid;
            self::$wc_apitoken = $apitoken;
        }

        /**
         * Send an SMS message to one or multiple phone numbers.
         *
         * @param array $phones   An array of phone numbers.
         * @param string $message  The message to be sent.
         * @return string|null     The message ID from the SMS API, or null on failure.
         */
        public static function send_sms(array $phones, string $message): ?string {
            $multiple_numbers = count($phones) > 1;
            $url = $multiple_numbers ? 'https://api.mobilesasa.com/v1/send/bulk' : 'https://api.mobilesasa.com/v1/send/message';
            $phone_param = $multiple_numbers ? 'phones' : 'phone'; 
            
            // Convert array of phones to a comma-separated string
            $phone_string = implode(',', $phones);
            
            $postData = [
                'senderID' => self::$wc_senderid,
                'message'  => $message,
                $phone_param => $phone_string,
                'api_token' => self::$wc_apitoken
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url,
                CURLOPT_POST => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 400,
                CURLOPT_POSTFIELDS => http_build_query($postData)
            ]);

            $response = curl_exec($curl);

            if ($response === false) {
                // Handle curl error
                $error = curl_error($curl);
                error_log('cURL Error: ' . $error);
                curl_close($curl);
                return null;
            }

            curl_close($curl);

            if ($response) {
                $responseVals = json_decode($response, true);
                if (isset($responseVals['status']) && $responseVals['status'] == true) {
                    if (isset($responseVals['bulkId'])) {
                        return $responseVals['bulkId'];
                    } elseif (isset($responseVals['messageId'])) {
                        return $responseVals['messageId'];
                    }
                }
            }

            return null;
        }


        /**
         * Clean and format the provided phone numbers.
         *
         * @param array $phones An array of phone numbers.
         * @return array        The cleaned and formatted phone numbers.
         */
        public static function clean_phone(array $phones): array {
            $cleaned_phones = [];

            foreach ($phones as $phone) {
                $cleaned_phone = "254" . substr(preg_replace('/[^0-9]/', '', $phone), -9);
                $cleaned_phones[] = $cleaned_phone;
            }

            return $cleaned_phones;
        }

        /**
         * Send an SMS message to one or multiple phone numbers.
         *
         * @param string $phones   A comma-separated list of phone numbers.
         * @param string $message  The message to be sent.
         * @return int             A status code indicating the success or failure of the operation.
         */
        public static function wc_send_sms(string $phones, string $message): int {

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
        
         /**
         * Clean and format the provided phone numbers.
         *
         * @param string $phones A comma-separated list of phone numbers.
         * @return string        The cleaned and formatted phone numbers.
         */
        public static function wc_clean_phone(string $phones): string {
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
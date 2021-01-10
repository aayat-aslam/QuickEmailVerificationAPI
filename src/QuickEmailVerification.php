<?php
include_once ('./config.php');
include_once ('../vendor/autoload.php');


/**
 * Class to integrate Quick Email Verification APIs
 */
class QuickEmailVerification {
    /**
     * variable that will hold API endpoint
     */
    private $endpoint;

    /**
     * Variable that will hold private key
     */
    private $apiKey;

    /**
     * Constructor function
     */
    public function __construct($endPoint,$apiKey) {
        $this->endpoint = $endPoint;
        $this->apiKey = $apiKey;
    }

    /**
     * Function will verify single email entry
     * @email String
     */
    public function verify($email) {
        //return $this->endpoint;
        try {
            $client = new QuickEmailVerification\Client( $this->apiKey ); // Replace API_KEY with your API Key
            $quickemailverification = $client->quickemailverification();
            $response = $quickemailverification->verify($email); // Email address which need to be verified
            return $response;
            // return $this->decodeValidation( $response );
        } catch (Exception $e) {
            // print_r( $e->getMessage() . ' | ' . $e->getCode() );
            return $this->processHttpCode( $e->getCode() ); // any code other than 200 (say)
        }
        
    }

    /**
     * Function to verify bulk emails
     */
    public function bulkVerify() {

    }

    private function decodeValidation($qev_object) {
        
        // $this->processHttpCode($qev_object['code']); // for other than 200
        $remaining_messages = $this->remainingCost($qev_object['header']);
        
        if( $qev_object['body']['result'] == 'valid' ) { // positive

            $trueOfFalse = true;

            if( false ) { // false positive detected
                $trueOfFalse = false;
            }

            return [
                "success" => $trueOfFalse,
                "message" => ($trueOfFalse == true) ? "valid email address" : "invalid email address",
                "remaining messages" => $remaining_messages,
            ];
        } else { # negative

            $err = $this->processBody($qev_object['body']);

            if( $err['final_validity'] == true ) { # false negative detected

            }
    
            return [
                "success" => TRUE,
                "message" => "",
                "remaining messages" => $remaining_messages,
                "err" => $err
            ];

        }
        
        
    }
    
    /**
     * Function will process all situation related to QuickEmailVerification object response http code
     */
    private function processHttpCode($qev_object_code) {

        $httpCode = $qev_object_code;
    
        /*
            AS PER DOCUMENTATION
            SOURCE => http://docs.quickemailverification.com/email-verification-api/kick-start-with-email-validation-api
            ===================
            200 - Request is completed successfully
            400 - Server can not understand the request sent to it. This is kind of response can occur if parameters are passed wrongly.
            401 - Server can not verify your authentication to use API. Please check whether API key is proper or not.
            402 - You are running out of your credit limit.
            403 - Your account has been disabled.
            404 - Requested API can not be found on server.
            429 - Too many requests. Rate limit exceeded.
            500 - Internal Server Error.
         */
        switch ($httpCode) {
            case '200':
                return [
                    'success' => true,
                    'message' => 'Request is completed successfully.'
                ];
                break;
    
            case '400':
                return [
                    'success' => false,
                    'message' => 'Something went wrong. Please contact admin with Error code 0x00002', //'Server can not understand the request sent to it. This is kind of response can occur if parameters are passed wrongly.'
                ];
                break;
    
            case '401':
                return [
                    'success' => false,
                    'message' => 'Something went wrong. Please contact admin with Error code 0x00003', //'Server can not verify your authentication to use API. Please check whether API key is proper or not.'
                ];
                break;
    
            case '402':
                return [
                    'success' => false,
                    'message' => 'Something went wrong. Please contact admin with Error code 0x00004', // 'You are running out of your credit limit.'
                ];
                break;
    
            case '403':
                return [
                    'success' => false,
                    'message' => 'Something went wrong. Please contact admin with Error code 0x00005', // account is disabled
                ];
                break;
    
            case '404':
                return [
                    'success' => false,
                    'message' => 'Something went wrong, please contact admin with error code 0x00006', // Requested API can not be found on server.
                ];
                break;
    
            case '429':
                return [
                    'success' => false,
                    'message' => 'Too many requests. Rate limit exceeded. please try after sometimes or use bulk feature'
                ];
                break;
    
            case '500':
                return [
                    'success' => false,
                    'message' => 'Internal Server Error, please try again'
                ];
                break;
            
            default:
                return [
                    'success' => false,
                    'message' => 'Something went wrong. Please try again.'
                ];
                break;
        }
        
    }

    /**
     * message will return remaining number of messages
     */
    private function remainingCost( $qev_object_header ) {
        return $qev_object_header['X-QEV-Remaining-Credits'][0];
    }

    /**
     * Function will process response body of QuickEmailVerification
     * Source: http://docs.quickemailverification.com/getting-started/understanding-email-verification-result
     */
    private function processBody($qev_object_body) {
        
        /*
        switch ($qev_object_body['result']) {
            case 'invalid':
                # code...
                break;

            case 'unknown':
                # code...
                break;
            
            default:
                # code...
                break;
        }
        */

        /*

        Invalid_email	The syntax of the email address is invalid (Is not according to RFC standards).
        Invalid_domain	The domain used in an email address doesn't exist.
        rejected_email	SMTP server rejected email. Email account doesn't exist on receiving server.
        accepted_email	SMTP server accepted email.
        no_connect	Could not connect to receiving SMTP server.
        timeout	Session timeout occurred on the remote SMTP server. It happens when the receiving mail server is responding too slow.
        unavailable_smtp	Receiving SMTP server was not available to process a request.
        unexpected_error	Some unexpected error has occurred on the receiving SMTP server.
        no_mx_record	MX record of the domain doesn’t exist. // part of DNS record
        temporarily_blocked	Email address is temporary greylisted.
        exceeded_storage	Email account on receiving server has exceeded storage allocation.

        */

        $error = [
            'final_validity' => false,
        ];


        switch ($qev_object_body['reason']) {
            
            case 'invalid_email':
                $error['message'] = 'Invalid email address, please check';
                break;
            
            case 'invalid_domain':
                $error['message'] = 'The domain of email doesn\'t exists, please check';
                break;
        
            case 'rejected_email':
                $error['message'] = 'The email you are trying doesn\'t exists, please check';
                break;
    
            case 'accepted_email':
                $error['message'] = 'Valid email address'; // ***
                break;

            case 'no_connect':
                $error['message'] = 'Unable to verify this email, try with another email'; // unable to connection
                break;

            case 'timeout':
                $error['message'] = 'Unable to verify your email this time, please try again or try with another email'; // timeout
                break;

            case 'unavailable_smtp':
                $error['message'] = 'Email doesn\'t exists, try with other email address.'; // email server not responding
                break;

            case 'no_mx_record':
                $error['message'] = 'Email doesn\'t exists, please try with other email address';
                break;

            case 'temporarily_blocked':
                $error['message'] = 'Your email is temporarily blocked, contact your domain/email admin.';
                break;

            case 'exceeded_storage':
                $error['message'] = 'Unable to verify email, Please contact admin with error code 0x00007';
                break;
            
            default:
                $error['message'] = 'invalid email address, please check';
                break;
        }




        
        
        
        //     [success]  => invalid
        //     [reason] => invalid_email
        //     [disposable] => false
        //     [accept_all] => false
        //     [role] => false
        //     [free] => false
        //     [email] => dfsfsf
        //     [user] => 
        //     [domain] => dfsfsf
        //     [mx_record] => true
        //     [mx_domain] => true
        //     [safe_to_send] => true
        //     [did_you_mean] => 
        //     [success] => true
        //     [message] =>
    }


}

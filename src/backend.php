<?php
include_once ('./config.php');
include_once ('./QuickEmailVerification.php');


$r = isset($_REQUEST['r']) ? $_REQUEST['r'] : '';

if( empty($r) ) {
    $arrResponse = ['success' => false, 'message' => 'required parameter missing'];
    header($_SERVER['SERVER_PROTOCOL'] . " 200 OK");
    header('Content-Type: application/json');
    echo json_encode($arrResponse);
    exit();
}

switch ($r) {
    case 'verifyEmail':
        $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
        
        if( empty($email) ) {
            $arrResponse = ['success' => false, 'message' => 'required parameter missing'];
            header($_SERVER['SERVER_PROTOCOL'] . " 200 OK");
            header('Content-Type: application/json');
            echo json_encode($arrResponse);
            exit();
        }

        $apiKey = $GLOBALS['API_KEY'];
        $endPoint = $GLOBALS['endpoint'];
        $qev = new QuickEmailVerification( $endPoint, $apiKey );
        $response = $qev->verify($email);

        // print_r( $response ); die;

        header($_SERVER['SERVER_PROTOCOL'] . " 200 OK");
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();

        break;

    case 'value':
        # code...
        break;
    
    default:
        $arrResponse = ['success' => false, 'message' => 'no such api exists'];
        header($_SERVER['SERVER_PROTOCOL'] . " 200 OK");
        header('Content-Type: application/json');
        echo json_encode($arrResponse);
        exit();
        break;
}
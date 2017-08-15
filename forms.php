<?php

defined( 'NODEMON_RUNNING') || die( 'Access denied.' );

if( isset( $_POST['formid'] ) ) {
    $formid = $_POST['formid'];

    if( $formid == 'connect' and $nodeconfig['services']['connect'] ) {
        $params = array( $_POST['address'], 'onetry' );
        send_request(
            create_request( 'addnode', $params ),
            $nodeconfig['username'],
            $nodeconfig['password'],
            $nodeconfig['serverurl']
        );

        $formresult = '<br><font color="green">Connection request sent to ' . $_POST['address'] . '.</font>';
    }

    if( $formid == 'verify' and $nodeconfig['services']['verify'] ) {
        $params = array( $_POST['address'], $_POST['signature'], $_POST['message'] );
        $response = send_request(
            create_request( 'verifymessage', $params ),
            $nodeconfig['username'],
            $nodeconfig['password'],
            $nodeconfig['serverurl']
        );

        if( $response['result'] ) {
            $formresult = '<br><font color="green">The entered signature is valid.</font>';
        } else {
            $formresult = '<br><font color="red">Signature verification FAILED.</font>';
        }
    }

    if( $formid == 'broadcast' and $nodeconfig['services']['broadcast'] ) {
        $params = array( $_POST['transaction'] );
        $response = send_request(
            create_request( 'sendrawtransaction', $params ),
            $nodeconfig['username'],
            $nodeconfig['password'],
            $nodeconfig['serverurl']
        );

        if( $response['error']['message'] ) {
            $formresult = '<br><font color="red">Error: ' . $response['error']['message'] . '.</font>';
        } else {
            $formresult = '<br><font color="green">Broadcasted transaction ' . $response['result'] . '.</font>';
        }

    }
}

?>
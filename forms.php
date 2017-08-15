<?php

defined( 'NODEMON_RUNNING') || die( 'Access denied.' );

function escapeinput( $input ) {
    return preg_replace( '/[^A-Za-z0-9]/', '', $input );
}

if( isset( $_POST['formid'] ) ) {
    $formid = escapeinput( $_POST['formid'] );

    if( $formid == 'broadcast' and $nodeconfig['broadcast'] ) {
        $params = array(
            escapeinput( $_POST['transaction'] )
        );
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

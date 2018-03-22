<?php

function format_bytes( $size, $precision = 2 ) {
    $base = log( $size, 1024 );
    $suffixes = array( '', 'KB', 'MB', 'GB', 'TB' );

    return round( pow( 1024, $base - floor( $base ) ), $precision ) .' '. $suffixes[ floor( $base ) ];
}

// https://stackoverflow.com/a/19680778
function seconds_to_time($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}

function decode_services( $flags ) {
    $set = '';

    $services = [
        ['NONE', 0],
        ['NETWORK', (1 << 0)],
        ['GETUTXO', (1 << 1)],
        ['BLOOM',   (1 << 2)],
        ['WITNESS', (1 << 3)],
        ['XTHIN',   (1 << 4)],
    ];

    foreach( $services as $service ) {
        if( ($flags & $service[1] ) != 0 ) {
            $set .= $service[0].' ';
        }
    }

    return $set;
}

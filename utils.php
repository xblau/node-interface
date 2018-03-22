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
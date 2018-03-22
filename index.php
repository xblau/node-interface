<?php

$startscript = microtime( true );

define( 'NODEMON_RUNNING', true );

if( file_exists( 'config.php' ) ) {
    require 'config.php';
} else {
    die( 'Configuration file (config.php) not found.' );
}

require 'utils.php';

if( !isset( $formid ) ) {
    require 'requests.php';
    require 'forms.php';
}

$pruned = $getbcinfo['result']['pruned'] ? 'true' : 'false';
$cpeers = count( $getpeerinfo['result'] );
$bpeers = count( $listbanned['result'] );

?>
<!DOCTYPE html>
<title><?php echo $nodeconfig['pagetitle']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="shortcut icon" href="favicon.ico" >
<link rel="stylesheet" href="style.css">
<?php
if( !isset( $formid ) && $nodeconfig['autorefresh'] > 0 ) {
    printf( '<meta http-equiv="refresh" content="%s" >', $nodeconfig['autorefresh'] );
}
?>

<body>
    <h1><?php echo $nodeconfig['pagetitle']; ?></h1>

    <?php

    if( isset( $formresult ) ) {
        echo $formresult;
        echo '<br><br><a href="">Return to the main page</a>';
        exit;
    }

    if( isset($getnetinfo['error']['code']) ) {
        die( 'Request failed with message: ' . $getnetinfo['error']['message'] );
    } elseif( !isset( $getnetinfo['result']['version'] ) ) {
        die( 'Something went wrong (getnetworkinfo failed). Check your config and try again.' );
    }

    echo $nodeconfig['pagedesc'] . PHP_EOL;

    ?>

    <br>

    <a id="about"></a>
    <fieldset>
        <legend>ABOUT THIS NODE</legend>
        <b>Node version:</b> <code><?php echo $getnetinfo['result']['version'].' ('.$getnetinfo['result']['protocolversion'].')';?></code><br>
        <b>Subversion:</b> <code><?php echo $getnetinfo['result']['subversion']; ?></code><br>
        <b>Local services:</b> <code><?php echo $getnetinfo['result']['localservices']; ?></code><br>
        <b>Relay fee:</b> <code><?php echo $getnetinfo['result']['relayfee']; ?></code>
        <?php

        if( isset( $uptime ) ) {
            printf( "<br><b>Uptime:</b> <code>%s</code>\n", seconds_to_time( $uptime['result'] ) );
        }

        ?>
    </fieldset><br>

    <a id="blockchaininfo"></a>
    <fieldset>
        <legend>BLOCKCHAIN INFO</legend>
        <b>Chain:</b> <code><?php echo $getbcinfo['result']['chain']; ?></code><br>
        <b>Blocks:</b> <code><?php echo $getbcinfo['result']['blocks']; ?></code><br>
        <b>Headers:</b> <code><?php echo $getbcinfo['result']['headers']; ?></code><br>
        <b>Difficulty:</b> <code><?php echo $getbcinfo['result']['difficulty']; ?></code><br>
        <b>Median time:</b> <code><?php echo date('d/m/Y H:i:s', $getbcinfo['result']['mediantime'] ); ?></code><br>
        <?php

        if(isset($getbcinfo['result']['size_on_disk'])) {
            printf("<b>Size on disk:</b> <code>%s</code><br>\n", format_bytes($getbcinfo['result']['size_on_disk']));
        }

        ?>
        <b>Pruned:</b> <code><?php echo $pruned; ?></code>
    </fieldset><br>

    <a id="mempool"></a>
    <fieldset>
        <legend>TX MEMORY POOL INFO</legend>
        <b>Transactions:</b> <code><?php echo $getmpinfo['result']['size']; ?></code><br>
        <b>Size:</b> <code><?php
            if( $getmpinfo['result']['size'] == 0 ) {
                echo "empty";
            } else {
                echo format_bytes( $getmpinfo['result']['bytes'] );
            }
        ?></code><br>
    </fieldset><br>

    <a id="netusage"></a>
    <fieldset>
        <legend>NETWORK USAGE</legend>
        <b>Total received:</b> <code><?php echo format_bytes( $getnettotals['result']['totalbytesrecv'] ); ?></code><br>
        <b>Total sent:</b> <code><?php echo format_bytes( $getnettotals['result']['totalbytessent'] ); ?></code><br>
    </fieldset><br>

    <?php echo $nodeconfig['broadcast'] ? '' : '<!--' ?>
    <a id="broadcast"></a>
    <fieldset>
        <legend>BROADCAST RAW TRANSACTION</legend>
        <form method="post">
            <input type="hidden" name="formid" value="broadcast">
            Raw transaction data:<br>
            <textarea name="transaction" rows="4" cols="50" required></textarea><br><br>
            <input type="submit" value="Broadcast">
        </form>
    </fieldset><br>
    <?php echo $nodeconfig['broadcast'] ? '' : '-->' ?>

    <a id="peers"></a>
    <fieldset>
        <legend><?php printf( 'CONNECTED PEERS (%s)', $cpeers ); ?></legend>
        <table>
            <tr>
                <th>addr</th>
                <th>services</th>
                <th>conntime</th>
                <th>version</th>
                <th>subver</th>
                <th>inbound</th>
                <th>banscore</th>
            </tr>
            <?php
            $tinbound = 0; $toutbound = 0;

            foreach( $getpeerinfo['result'] as $peer ) {
                $inbound = $peer['inbound'] ? 'true' : 'false';
                $conntime = date('d/m/Y H:i:s', $peer['conntime'] );

                $servbits  = hexdec('0x' . $peer['services']);
                $servnames = decode_services($servbits);

                echo '<tr>';
                printf( '<td>%s</td>', $peer['addr'] );
                printf( '<td title="%s">%s</td>', $servnames, '0x'.dechex($servbits) );
                printf( '<td title="%s">%s</td>', $conntime, $peer['conntime'] );
                printf( '<td>%s</td>', $peer['version'] );
                printf( '<td>%s</td>', $peer['subver'] );
                printf( '<td>%s</td>', $inbound );
                printf( '<td>%s</td>', $peer['banscore'] );
                echo '</tr>';

                $peer['inbound'] ? $tinbound++ : $toutbound++;
            }

            ?>
        </table>
        <br><b>Total inbound/outbound:</b> <?php echo "$tinbound/$toutbound"; ?>
    </fieldset><br>

    <a id="banned"></a>
    <fieldset>
        <legend><?php printf( 'BANNED PEERS (%s)', $bpeers ); ?></legend>
        <table>
            <tr>
                <th>address</th>
                <th>banned since</th>
                <th>banned until</th>
                <th>ban reason</th>
            </tr>
            <?php

            foreach( $listbanned['result'] as $peer ) {
                $bansince = date('d/m/Y H:i:s', $peer['ban_created'] );
                $banuntil = date('d/m/Y H:i:s', $peer['banned_until'] );

                echo '<tr>';
                printf( '<td>%s</td>', $peer['address'] );
                printf( '<td title="%s">%s</td>', $bansince, $peer['ban_created'] );
                printf( '<td title="%s">%s</td>', $banuntil, $peer['banned_until'] );
                printf( '<td>%s</td>', $peer['ban_reason'] );
                echo '</tr>';
            }

            ?>
        </table>
    </fieldset><br>

    <?php
    $endscript = microtime( true );
    $loadtime = $endscript - $startscript;
    ?>
    <div class="footer">
        Made by <a href="https://github.com/xblau">xBlau</a>.
        Powered by Core. Generated in <?php echo number_format( $loadtime, 4 ) ?> seconds.
        Source code <a href="https://github.com/xblau/node-interface">here</a>.
        <br><br>
    </div>
</body>

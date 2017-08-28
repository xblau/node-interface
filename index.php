<?php

$startscript = microtime( true );

define( 'NODEMON_RUNNING', true );

if( file_exists( 'config.php' ) ) {
    require 'config.php';
} else {
    die( 'Configuration file (config.php) not found.' );
}

if( !isset( $formid ) ) {
    require 'requests.php';
    require 'forms.php';
}

function format_bytes( $size, $precision = 2 ) {
    $base = log( $size, 1024 );
    $suffixes = array( '', 'KB', 'MB', 'GB', 'TB' );

    return round( pow( 1024, $base - floor( $base ) ), $precision ) .' '. $suffixes[ floor( $base ) ];
}

$testnet = $getinfo['result']['testnet'] ? 'true' : 'false';
$pruned  = $getbcinfo['result']['pruned'] ? 'true' : 'false';

?>
<!DOCTYPE html>
<title><?php echo $nodeconfig['pagetitle']; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
if( !isset( $formid ) && $nodeconfig['autorefresh'] > 0 ) {
    echo '<meta http-equiv="refresh" content="'. $nodeconfig['autorefresh'] .'" >';
}
?>

<style>
body {
    width:900px;
    margin:0 auto;
    font-family: 'Consolas';
    font-size: 14px;
}

input[type="text"], textarea {
    font-family: 'Consolas';
    font-size: 12px;
}

table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
    text-align: center;
}

th, td {
    padding: 5px;
}

tr:hover {
    background-color: #f5f5f5
}

th {
    background-color: #4CAF50;
    color: white;
}
</style>

<body>
    <h1><?php echo $nodeconfig['pagetitle']; ?></h1>

    <?php

    if( isset( $formresult ) ) {
        echo $formresult;
        echo '<br><br><a href="">Return to the main page</a>';
        exit;
    }

    if( !isset( $getinfo['result']['version'] ) ) {
        die( 'Something went wrong (getinfo failed). Check your config and try again.' );
    }

    echo $nodeconfig['pagedesc'] . PHP_EOL;

    ?>

    <br>

    <a name="about"></a>
    <fieldset>
        <legend>ABOUT THIS NODE</legend>
        <b>Node version:</b> <code><?php echo $getinfo['result']['version'].' ('.$getinfo['result']['protocolversion'].')';?></code><br>
        <b>Subversion:</b> <code><?php echo $getnetinfo['result']['subversion']; ?></code><br>
        <b>Local services:</b> <code><?php echo $getnetinfo['result']['localservices']; ?></code><br>
        <b>Testnet:</b> <code><?php echo $testnet; ?></code><br>
        <b>Relay fee:</b> <code><?php echo $getinfo['result']['relayfee']; ?> LTC</code>
    </fieldset><br>

    <a name="blockchaininfo"></a>
    <fieldset>
        <legend>BLOCKCHAIN INFO</legend>
        <b>Chain:</b> <code><?php echo $getbcinfo['result']['chain']; ?></code><br>
        <b>Blocks:</b> <code><?php echo $getbcinfo['result']['blocks']; ?></code><br>
        <b>Headers:</b> <code><?php echo $getbcinfo['result']['headers']; ?></code><br>
        <b>Difficulty:</b> <code><?php echo $getbcinfo['result']['difficulty']; ?></code><br>
        <b>Median time:</b> <code><?php echo date('d/m/Y H:i:s', $getbcinfo['result']['mediantime'] ); ?></code><br>
        <b>Pruned:</b> <code><?php echo $pruned; ?></code>
    </fieldset><br>

    <a name="mempool"></a>
    <fieldset>
        <legend>TX MEMORY POOL INFO</legend>
        <b>Transactions:</b> <code><?php echo $getmpinfo['result']['size']; ?></code><br>
        <b>Size:</b> <code><?php echo format_bytes( $getmpinfo['result']['bytes'] ); ?></code><br>
    </fieldset><br>

    <a name="netusage"></a>
    <fieldset>
        <legend>NETWORK USAGE</legend>
        <b>Total received:</b> <code><?php echo format_bytes( $getnettotals['result']['totalbytesrecv'] ); ?></code><br>
        <b>Total sent:</b> <code><?php echo format_bytes( $getnettotals['result']['totalbytessent'] ); ?></code><br>
    </fieldset><br>

    <?php echo $nodeconfig['broadcast'] ? '' : '<!--' ?>
    <a name="broadcast"></a>
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

    <a name="peers"></a>
    <fieldset>
        <legend>CONNECTED PEERS (<?php echo count( $getpeerinfo['result'] ); ?>)</legend>
        <table style="width:100%">
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

                echo '<tr>';
                echo '    <td>' . $peer['addr'] . '</td>';
                echo '    <td>' . $peer['services'] . '</td>';
                echo '    <td title="'.$conntime.'">' . $peer['conntime'] . '</td>';
                echo '    <td>' . $peer['version'] . '</td>';
                echo '    <td>' . $peer['subver'] . '</td>';
                echo '    <td>' . $inbound . '</td>';
                echo '    <td>' . $peer['banscore'] . '</td>';
                echo '</tr>';

                $peer['inbound'] ? $tinbound++ : $toutbound++;
            }

            ?>
        </table>
        <br><b>Total inbound/outbound:</b> <?php echo "$tinbound/$toutbound"; ?>
    </fieldset><br>

    <a name="banned"></a>
    <fieldset>
        <legend>BANNED PEERS (<?php echo count( $listbanned['result'] ); ?>)</legend>
        <table style="width:100%">
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
                echo '    <td>' . $peer['address'] . '</td>';
                echo '    <td title="'.$bansince.'">' . $peer['ban_created'] . '</td>';
                echo '    <td title="'.$banuntil.'">' . $peer['banned_until'] . '</td>';
                echo '    <td>' . $peer['ban_reason'] . '</td>';
                echo '</tr>';
            }

            ?>
        </table>
    </fieldset><br>

    <?php
    $endscript = microtime( true );
    $loadtime = $endscript - $startscript;
    ?>
    <i>Made by xBlau. Powered by Litecoin Core. Generated in
    <?php echo number_format( $loadtime, 4 ) ?> seconds.
    Source code <a href="https://github.com/xblau/node-interface">here</a>.
    </i><br><br>
</body>

<?php
require_once __DIR__ . "/vendor/autoload.php";


$telnet = new \Core\Telnet('10.0.20.222');

$comm = $telnet->getDiscoveredOnu(1);

var_dump($comm);

/*
$user = 'admin';
$pass ='pnetsenhanova2014';

$socket = fsockopen('192.168.21.54', 23);
echo fgets($socket);
sleep(1);
fputs($socket, "{$user}\r\n");
sleep(1);
fputs($socket, "{$pass}\r\n");
sleep(1);


$com = "show interface gpon 1/1/5";

fputs($socket, "{$com}\r\n");
stream_set_timeout($socket, 2);

$timeoutCount = 0;
while(!feof($socket)){
    $content = fgets($socket);
    print "$content";

    # If the router say "press space for more", send space char:
    if (preg_match("/--More--/", $content) ){ // IF current line contain --More-- expression,

        fputs ($socket, ' '); // sending space char for next part of output.

    } # The "more" controlling part complated.

    $end = preg_match("/END/", $content);
    $info = stream_get_meta_data($socket);

    if ($info['timed_out']) { // If timeout of connection info has got a value, the router not returning a output.
        $timeoutCount++; // We want to count, how many times repeating.
    }
    //if ($timeoutCount >5){ // If repeating more than 2 times,
    if ($end == 1 || $timeoutCount > 2){ // If repeating more than 2 times,
        print "\r\n";
        break;   // the connection terminating..
    }

}

fclose($socket);



//var_dump($info);
//echo fgets($socket);
*/

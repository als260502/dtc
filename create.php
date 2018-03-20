<?php
require_once __DIR__ . "/vendor/autoload.php";


$telnet = new \Core\Telnet('10.0.20.222');

$comm = $telnet->managePort();

var_dump($comm);


$user = 'admin';
$pass ='pnetsenhanova2014';
/*
$socket = fsockopen('172.17.75.2', 23);
echo fgets($socket);
sleep(1);
fputs($socket, "{$user}\r\n");
sleep(1);
fputs($socket, "{$pass}\r\n");
sleep(1);

/*
$com = "config
interface gpon 1/1/1
  onu 0
  name AND42
  serial-number DACM0000810A
  service-profile teste line-profile SIP
  ethernet 1
   negotiation
   no shutdown
   native vlan vlan-id 1000
   mac-limit 255
  !
 !
 service-port 1 gpon 1/1/1 onu 0 gem 1 match vlan vlan-id 1000 action vlan add vlan-id 60
 commit
   !
 !";


$com = 'show mac-address-table vlan 3200 | include "service-port-21"';
fputs($socket, "{$com}\r\n");
stream_set_timeout($socket, 2);
fgets($socket);

$timeoutCount = 0;
while(!feof($socket)){
    $content = fgets($socket);
    print "$content";

    $end = preg_match("/END/", $content);
    $info = stream_get_meta_data($socket);

    if ($info['timed_out']) { // If timeout of connection info has got a value, the router not returning a output.
        $timeoutCount++; // We want to count, how many times repeating.
    }
    //if ($timeoutCount >5){ // If repeating more than 2 times,
    if ($end == 1 || $timeoutCount > 5){ // If repeating more than 2 times,
        print "\r\n";
        break;   // the connection terminating..
    }

    if (preg_match("/--More--/", $content)) { // IF current line contain --More-- expression,

        fputs($socket, ' '); // sending space char for next part of output.

    } # The "more" controlling part complated.
/*
    if(preg_match("/Aborted:/", $content))
    {

        preg_match("/['-:,; \w]+/", $content, $erro);
        echo $erro[0];
        break;
    }

    if(preg_match("/Commit complete./", $content))
    {

        preg_match("/Commit complete./", $content, $complete);
        echo $complete[0];
        break;

    }


}






/*
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
*/
//fclose($socket);




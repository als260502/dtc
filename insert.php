<?php
require_once __DIR__ . "/vendor/autoload.php";

$ip = '172.17.75.2';
$result = '';
$fp = fsockopen($ip, 23);
fgets($fp);
sleep(1);

fputs($fp, "admin\r\n");
sleep(1);
fputs($fp, "pnetsenhanova2014\r\n");
//fputs($fp,"admin\r\n");
sleep(1);

$olt = 1;
$s_p = 1;

fputs($fp, "show interface gpon 1/1/{$olt} onu\r\n");
sleep(1);


stream_set_timeout($fp, 2);
$timeoutCount = 0;


while (!feof($fp)) {
    $content = fgets($fp);
    $content = str_replace("\r", '', $content);
    $content = str_replace("\n", "", $content);
    $content = str_replace("\t", "", $content);
    $rs = trim($content) . "\n";
    $rs = str_replace(" ", '', $rs);

    $id = preg_match("/^[0-9]+/", $content, $i);
    if (!empty($i)) $index[] = $i[0];

    $sn = preg_match("/DACM0[A-Fa-f0-9]+/", $rs, $s);
    if (!empty($s)) $serial[] = $s[0];

    //if($id == 1){
    if (preg_match("/Up/", $rs)) {
        preg_match("/Up/", $rs, $st);
        $state[] = $st[0];
    }
    if (preg_match("/Down/", $rs)) {
        preg_match("/Down/", $rs, $st);
        $state[] = $st[0];
    }
    //}


    $n = preg_match("/[A-Z0-9_o-s]+$/", $rs, $nm);
    if (!empty($nm)) $name[] = $nm[0];

    print $rs;


    # If the router say "press space for more", send space char:
    if (preg_match("/--More--/", $content)) { // IF current line contain --More-- expression,

        $str = preg_replace('/--More--/', '', $rs);
        //print $rs."\n".$str;
        //$n = preg_match("/[a_0-9A-Z]+\w$/", $str, $nm);
        $n = preg_match("/[A-Z0-9_o-s]+$/", $str, $nm);
        if (!empty($nm)) $name[] = $nm[0];

        fputs($fp, " "); // sending space char for next part of output.
    } # The "more" controlling part complated.

    $end = preg_match("/END/", $rs);
    $info = stream_get_meta_data($fp);

    if ($info['timed_out']) { // If timeout of connection info has got a value, the router not returning a output.
        $timeoutCount++; // We want to count, how many times repeating.
    }
    //if ($timeoutCount >5){ // If repeating more than 2 times,
    if ($end == 1 || $timeoutCount > 7) { // If repeating more than 2 times,
        break;   // the connection terminating..
    }

}

$status = array_shift($state);

//print_r($serial);



$remove = array('admin', 'OSCLI', 'CLI', 'DM4610-AL607', 'onu', 'Name', 'AL607');
$remove = array('admin', 'OSCLI', 'CLI', 'DM4610-G321', 'onu', 'Name', '33');
$res = array_diff($name, $remove);

$res1 = array_shift($res);
//print_r($res);

//ksort($res);
fclose($fp);
//echo nl2br($result);

foreach ($res as $value)
    $nme[] = $value;

function db()
{
    $host = 'localhost';
    $db = 'datacom';
    $user = 'root';
    $pass = '';
    $charset = 'utf8';
    $collation = 'utf8_unicode_ci';
    try {
        $pdo = new \PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET_NAMES '$charset' COLLATE '$collation'");
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        return $pdo;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}



$conn = db();
$port = 1;
$vlan = 1000;
$serv = $s_p;
$olt = $olt;
//for ($i = 0; $i < count($serial); $i++) {

for ($i = 0; $i < count($serial) ; $i++) {
    $vlan = 1000 + $i;
    $onu = 0 + $i;
    $sp = $serv;
    $port = 1;
    $technology = "UTP";
    $active = 1;

    //$data = ['onu_index'=> $onu, 'onu_name' => $nme[$i],'serial_number'=>$serial[$i], 'port_number'=>$port, 'vlan'=>$vlan,'service_port'=> $sp, 'olt_id'=>$olt];
    $data = [$onu, $nme[$i], $serial[$i], $port, $vlan, $sp, $olt];

    //print "$name[$i]\n";
    // print_r($data);
    /*
        if ($sp == 1) {
            $data[4] = 2;
            $technology = "HPNA";
        }

        if ($sp == 21) {
            $data[0] = 0;
            $data[1]= 'FC161';
            $data[2]= 'DACM0000164F';
            $data[4]= 1000;
            $i = 19;
        }

        if ($sp == 23) {
            $data[4] = 2;

        }

        if ($sp == 24) {
            $data[0] = 21;
            $data[1]= 'D400';
            $data[2]= 'DACM00001743';
            $data[4]= 1020;
            $i = 21;
        }
    */

    /*
        $sql = "INSERT INTO gpons (onu_index, onu_name, serial_number, port_number, vlan, service_port, olt_id)VALUES(?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);

        $stmt->execute($data);
        $gpon_id = $conn->lastInsertId();


        $sqlPort = "INSERT INTO ethernets(eth, technology, active, gpon_id)VALUES(:eth, :technology, :active, :gpon_id)";

        $stmtp = $conn->prepare($sqlPort);
        $stmtp->bindParam(':eth', $port);
        $stmtp->bindParam(':technology', $technology);
        $stmtp->bindParam(':active', $active);
        $stmtp->bindParam(':gpon_id', $sp);

        $stmtp->execute();
    */

}


/*
profile gpon line-profile VT12

no upstream-fec

tcont 1 bandwidth-profile MAX_1081M

gem 1 tcont 1

map ETHERNET ethernet 1 vlan 1318 cos any

top

interface gpon 1/1/3

onu 18

line-profile VT12

commit

top


do show interface gpon 1/1/3 onu 10 ethernet
*/





<?php
require_once __DIR__ . "/vendor/autoload.php";

$removeDataFromArray = function($loops, array $array){
    for($i=0; $i < $loops; $i++)
        $res = array_shift($array);
    return $array;
};

$ip = '172.17.113.2';
$result = '';
$fp = fsockopen($ip, 23);
fgets($fp);
sleep(1);

fputs($fp, "admin\r\n");
sleep(1);
fputs($fp, "pnetsenhanova2014\r\n");
//fputs($fp,"admin\r\n");
sleep(1);

$olt = 7;
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

    $namePattern=($ip == '172.17.113.2')?"/[_a0-9A-Z]+\w$/" :"/[A-Z0-9_o-s]+$/";

    $n = preg_match("{$namePattern}", $rs, $nm);
    if (!empty($nm)) $name[] = $nm[0];

    print $rs;


    # If the router say "press space for more", send space char:
    if (preg_match("/--More--/", $content)) { // IF current line contain --More-- expression,

        $str = preg_replace('/--More--/', '', $rs);
        //print $rs."\n".$str;

        $n = preg_match("{$namePattern}", $str, $nm);
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


$rst = $removeDataFromArray(2,$name);

//print_r($serial);

print_r ($name);
//print_r($rst);

//ksort($res);
fclose($fp);
//echo nl2br($result);
/*
foreach ($res as $value)
    $nme[] = $value;
*/
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
$vlan = 1900;
$serv = 1;
$sp = 900;
$olt = 14;
$nme = $rst;
for ($i = 0; $i < count($serial); $i++) {
    $onu = 0 + $i;
    $port = 1;
    $technology = "UTP";
    $active = 1;

    $data = [$onu, $nme[$i], $serial[$i], $port, $vlan, $sp, $olt];

    if($sp == 907 || $sp == 917)
       $i--;

    if($sp == 908 || $sp == 918)
        $data[3] = 2;

    $sql = "INSERT INTO gpons (onu_index, onu_name, serial_number, port_number, vlan, service_port, olt_id)VALUES(?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
    $gpon_id = ($data[3] == 1)?$conn->lastInsertId():$gpon_id;

    $dataEth = [$data[3], $technology, $active, $gpon_id];
    $sqlPort = "INSERT INTO ethernets(eth, technology, active, gpon_id)VALUES(?, ?, ?, ?)";
    $stmtp = $conn->prepare($sqlPort);
    $stmtp->execute($dataEth);

    print_r($data);

    $sp++;
    $vlan++;

}












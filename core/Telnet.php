<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 05/10/2017
 * Time: 07:50
 */

namespace Core;


use App\Models\Gpon;

class Telnet
{
    private $socket = null;
    private $comand = null;
    private $result;
    private $host;
    private $user;
    private $pass;
    private $configString;
    private $servicePort;
    private $lineProfile;
    private $kompresor;

    public function __construct($hostIP)
    {
        $this->host = $hostIP;
        $this->user = 'admin';
        $this->pass = 'pnetsenhanova2014';
    }

    private function openSocket()
    {

        $this->socket = fsockopen($this->host, 23, $errno, $errstr, 5);

        //var_dump($this->socket);

        if (!$this->socket) {
            $this->socket = false;
            return ("ERRO: {$errno}\n{$errstr}\n");
        }

        //stream_set_timeout($this->socket, 1);
        fgets($this->socket);
        sleep(1);
        fputs($this->socket, "{$this->user}\r\n");
        sleep(1);
        fputs($this->socket, "{$this->pass}\r\n");
        sleep(1);

        return $this->socket;


    }

    private function closeSocket($exit = 1)
    {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = NULL;
        }
    }

    private function executeComand($comand)
    {
        if (!$this->openSocket()) return;
        if ($this->socket) {
            fputs($this->socket, "{$comand}\r\n");
            stream_set_timeout($this->socket, 2);
        }
    }

    private function doComand($command)
    {

        $timeoutCount = 0;
        $this->executeComand($command);
        while (!feof($this->socket)) {
            $this->result = fgets($this->socket);
            //print "$this->result";

            # If the router say "press space for more", send space char:
            if (preg_match("/--More--/", $this->result)) { // IF current line contain --More-- expression,

                fputs($this->socket, ' '); // sending space char for next part of output.

            } # The "more" controlling part complated.

            $end = preg_match("/END/", $this->result);
            $info = stream_get_meta_data($this->socket);

            if ($info['timed_out']) { // If timeout of connection info has got a value, the router not returning a output.
                $timeoutCount++; // We want to count, how many times repeating.
            }
            //if ($timeoutCount >5){ // If repeating more than 2 times,
            if ($end == 1 || $timeoutCount > 2) { // If repeating more than 2 times,
                print "\r\n";
                break;   // the connection terminating..
            }

        }

    }

    public function getDiscoveredOnu($olt)
    {

        $command = "show interface gpon 1/1/{$olt} discovered-onus";
        $pattern = "/DACM[A-Z0-9]+/";
        $timeoutCount = 0;
        $this->executeComand($command);
        while (!feof($this->socket)) {
            $result = fgets($this->socket, 128);
            //print $result;
            if (preg_match($pattern, $result)) {
                $rt = trim($result);
                preg_match($pattern, $rt, $this->result);
            }

            $end = preg_match("/END/", $result);
            $info = stream_get_meta_data($this->socket);

            if ($info['timed_out']) { // If timeout of connection info has got a value, the router not returning a output.
                $timeoutCount++; // We want to count, how many times repeating.
            }

            if ($end == 1 || $timeoutCount > 1) { // If repeating more than 2 times,
                print "\r\n";
                break;   // the connection terminating..
            }

        }

        return $this->result[0];

    }

    public function configOnu($chassi, $stringData, $placa)
    {

    }


    private function onuStringConfig(Gpon $gpon, $tecnologia)
    {


    }


    private function config(Gpon $gpon,$tecnologia)
    {

        if ($tecnologia == "UTP") {
            $this->configString .= "onu {$gpon->onu_index}\n";  
            $this->configString .= "name {$gpon->onu_name}\n";  
            $this->configString .= "serial-number {$gpon->serial_number}\n";
            $this->configString .= "service-profile Bridge line-profile Bridge-UTP\n";
            $this->configString .= "ethernet 1\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "native vlan vlan-id {$gpon->vlan}\n";   
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";

            $this->servicePort .= "service-port {$gpon->service_port} gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 1 match vlan vlan-id {$gpon->vlan} action vlan add vlan-id {$gpon->olt->qnq}\n";

            $this->kompressor .= "# {$gpon->onu_name} {$gpon->serial_number}\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} {$gpon->vlan} '[gw de telefonia]'\n";
            $this->kompressor .= "\n";

        } else if ($tecnologia == "HPNA") {
            $this->configString .= "onu {$gpon->onu_index}\n";
            $this->configString .= "name {$gpon->onu_name}\n";
            $this->configString .= "serial-number {$gpon->serial_number}\n";
            $this->configString .= "service-profile Bridge line-profile HPNA_{$gpon->onu_name}\n";
            $this->configString .= "ethernet 1\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";

            $this->service_port .= "service-port {$gpon->service_port} gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 1 match vlan vlan-id 2 action vlan add vlan-id {$gpon->olt->qnq}\n";

            $this->service_port .= "service-port ".($gpon->service_port + 1)."gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 2 match vlan vlan-id {$gpon->vlan} action vlan add vlan-id {$gpon->olt->qnq}\n";

            $this->lineProfile .= "profile gpon line-profile HPNA_{$gpon->onu_name}\n";
            $this->lineProfile .= "no upstream-fec\n";
            $this->lineProfile .= "tcont 1 bandwidth-profile HPNA_MGMT\n";
            $this->lineProfile .= "tcont 2 bandwidth-profile DADOS\n";
            $this->lineProfile .= "gem 1\n";
            $this->lineProfile .= "tcont 1 priority 0\n";
            $this->lineProfile .= "map HPNA\n";
            $this->lineProfile .= "ethernet 1 vlan 2 cos any\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "gem 2\n";
            $this->lineProfile .= "tcont 2 priority 0\n";
            $this->lineProfile .= "map PPPoE\n";
            $this->lineProfile .= "ethernet 1 vlan {$gpon->vlan} cos any\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "!\n";

            $this->kompressor .= "# {$gpon->onu_name} {$gpon->serial_number}\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} {$gpon->vlan} '[ip de telefonia]'\n";
            $this->kompressor .= "/etc/predialnet/sobe_hpna {$gpon->olt->qnq} '[gw do master]'\n";
            $this->kompressor .= "\n";


        } else if ($tecnologia == "HPNA/UTP") {


            $this->configString .= "onu {$gpon->onu_index}\n";
            $this->configString .= "name {$gpon->onu_name}\n";
            $this->configString .= "serial-number {$gpon->serial_number}\n";
            $this->configString .= "service-profile Bridge line-profile HPNA_{$gpon->onu_name}\n";
            $this->configString .= "ethernet 1\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "ethernet 2\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "native vlan vlan-id ".($gpon->vlan+1)."\n";
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";


            $this->service_port .= "service-port {$gpon->service_port} gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 1 match vlan vlan-id 2 action vlan add vlan-id {$gpon->olt->qnq}\n";
            $this->service_port .= "service-port ".($gpon->service_port+1)." gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 2 match vlan vlan-id {$gpon->vlan} action vlan add vlan-id {$gpon->olt->qnq}\n";
            $this->service_port .= "service-port ".($gpon->service_port+2)." gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 3 match vlan vlan-id ".($gpon->vlan+1)." action vlan add vlan-id {$gpon->olt->qnq}\n";

            $this->lineProfile .= "profile gpon line-profile HPNA_{$gpon->onu_name}\n";
            $this->lineProfile .= "no upstream-fec\n";
            $this->lineProfile .= "tcont 1 bandwidth-profile HPNA_MGMT\n";
            $this->lineProfile .= "tcont 2 bandwidth-profile DADOS\n";
            $this->lineProfile .= "tcont 3 bandwidth-profile DADOS\n";
            $this->lineProfile .= "gem 1\n";
            $this->lineProfile .= "tcont 1 priority 0\n";
            $this->lineProfile .= "map HPNA\n";
            $this->lineProfile .= "ethernet 1 vlan 2 cos any\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "gem 2\n";
            $this->lineProfile .= "tcont 2 priority 0\n";
            $this->lineProfile .= "map PPPoE-HPNA\n";
            $this->lineProfile .= "ethernet 1 vlan {$gpon->vlan} cos any\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "gem 3\n";
            $this->lineProfile .= "tcont 3 priority 0\n";
            $this->lineProfile .= "map PPPoE-UTP\n";
            $this->lineProfile .= "ethernet 2 vlan ".($gpon->vlan+1)." cos any\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "!\n";
            $this->lineProfile .= "!\n";

            $this->kompressor .= "# {$gpon->onu_name} {$gpon->serial_number}\n";
            $this->kompressor .= "# Porta 1 Rede HPNA\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} {$gpon->vlan} '[ip de telefonia]'\n";
            $this->kompressor .= "/etc/predialnet/sobe_hpna {$gpon->olt->qnq} '[gw do master]'\n";
            $this->kompressor .= "# Porta 2 Rede UTP ou SUB\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} ".($gpon->vlan+1)." '[ip de telefonia]'\n";
            $this->kompressor .= "\n";


        } else if ($tecnologia == "UTP/UTP") {

            $this->configString .= "onu {$gpon->onu_index}\n";
            $this->configString .= "name {$gpon->onu_name}\n";
            $this->configString .= "serial-number {$gpon->serial_number}\n";
            $this->configString .= "service-profile ONU-4Portas line-profile ONU-4Portas\n";
            $this->configString .= "ethernet 1\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "native vlan vlan-id {$gpon->vlan}\n";
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";
            $this->configString .= "ethernet 2\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "native vlan vlan-id ".($gpon->vlan+1)."\n";
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";

            $this->service_port .= "service-port {$gpon->service_port} gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 1 match vlan vlan-id {$gpon->vlan} action vlan add vlan-id {$gpon->olt->qnq}\n";
            $this->service_port .= "service-port ".($gpon->service_port+1)." gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 2 match vlan vlan-id ".($gpon->vlan+1)." action vlan add vlan-id {$gpon->olt->qnq}\n";

            $this->kompressor .= "# {$gpon->onu_name} {$gpon->serial_number}\n";
            $this->kompressor .= "# Porta 1 Rede do Predio\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} {$gpon->vlan} '[gw de telefonia]'\n";
            $this->kompressor .= "# Porta 2 SUB\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} ".($gpon->vlan+1)." '[gw de telefonia]'\n";
            $this->kompressor .= "\n";


        } else if ($tecnologia == "UTP/UTP/UTP") {

            $this->configString .= "onu {$gpon->onu_index}\n";
            $this->configString .= "name {$gpon->onu_name}\n";
            $this->configString .= "serial-number {$gpon->serial_number}\n";
            $this->configString .= "service-profile ONU-4Portas line-profile ONU-4Portas\n";
            $this->configString .= "ethernet 1\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "native vlan vlan-id {$gpon->vlan}\n";
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";
            $this->configString .= "ethernet 2\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "native vlan vlan-id ".($gpon->vlan+1)."\n";
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";
            $this->configString .= "ethernet 3\n";
            $this->configString .= "negotiation\n";
            $this->configString .= "no shutdown\n";
            $this->configString .= "native vlan vlan-id ".($gpon->vlan+2)."\n";
            $this->configString .= "mac-limit 255\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";
            $this->configString .= "!\n";

            $this->service_port .= "service-port {$gpon->service_port} gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 1 match vlan vlan-id {$gpon->vlan} action vlan add vlan-id {$gpon->olt->qnq}\n";
            $this->service_port .= "service-port ".($gpon->service_port+1)." gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 2 match vlan vlan-id ".($gpon->vlan+1)." action vlan add vlan-id {$gpon->olt->qnq}\n";
            $this->service_port .= "service-port ".($gpon->service_port+2)." gpon 1/1/{$gpon->olt->index} onu {$gpon->onu_index} gem 3 match vlan vlan-id ".($gpon->vlan+2)." action vlan add vlan-id {$gpon->olt->qnq}\n";

            $this->kompressor .= "# {$gpon->onu_name} {$gpon->serial_number}\n";
            $this->kompressor .= "# Porta 1 Rede do Predio\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} {$gpon->vlan} '[gw de telefonia]'\n";
            $this->kompressor .= "# Porta 2 Rede UTP ou SUB\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} ".($gpon->vlan+1)." '[gw de telefonia]'\n";
            $this->kompressor .= "# Porta 3 Rede UTP ou SUB\n";
            $this->kompressor .= "/etc/predialnet/sobe_interface {$gpon->olt->qnq} ".($gpon->vlan+2)." '[gw de telefonia]'\n";
            $this->kompressor .= "\n";

        }

    }

}
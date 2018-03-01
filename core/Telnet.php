<?php
/**
 * Created by PhpStorm.
 * User: Andre Souza
 * Date: 05/10/2017
 * Time: 07:50
 */

namespace Core;


class Telnet
{
    private $socket = null;
    private $comand = null;
    private $result;
    private $host;
    private $user;
    private $pass;
    private $info;

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

            if(!$this->socket){
                $this->socket = false;
                return("ERRO: {$errno}\n{$errstr}\n");
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
        if(!$this->openSocket())return;
        if ($this->socket) {
            fputs($this->socket, "{$comand}\r\n");
            stream_set_timeout($this->socket, 2);
        }
    }

    private function doComand($command){

        $timeoutCount = 0;
        $this->executeComand($command);
        while(!feof($this->socket)){
            $this->result = fgets($this->socket);
            //print "$this->result";

            # If the router say "press space for more", send space char:
            if (preg_match("/--More--/", $this->result) ){ // IF current line contain --More-- expression,

                fputs ($this->socket, ' '); // sending space char for next part of output.

            } # The "more" controlling part complated.

            $end = preg_match("/END/", $this->result);
            $info = stream_get_meta_data($this->socket);

            if ($info['timed_out']) { // If timeout of connection info has got a value, the router not returning a output.
                $timeoutCount++; // We want to count, how many times repeating.
            }
            //if ($timeoutCount >5){ // If repeating more than 2 times,
            if ($end == 1 || $timeoutCount > 2){ // If repeating more than 2 times,
                print "\r\n";
                break;   // the connection terminating..
            }

        }

    }

    public function getDiscoveredOnu($olt){

        $command = "show interface gpon 1/1/{$olt} discovered-onus";
        $pattern = "/DACM[A-Z0-9]+/";
        $timeoutCount = 0;
        $this->executeComand($command);
        while(!feof($this->socket)){
            $result = fgets($this->socket,128);
            //print $result;
            if(preg_match($pattern, $result)) {
                $rt = trim($result);
                preg_match($pattern, $rt, $this->result);
            }

            $end = preg_match("/END/", $result);
            $info = stream_get_meta_data($this->socket);

            if ($info['timed_out']) { // If timeout of connection info has got a value, the router not returning a output.
                $timeoutCount++; // We want to count, how many times repeating.
            }

            if ($end == 1 || $timeoutCount > 1){ // If repeating more than 2 times,
                print "\r\n";
                break;   // the connection terminating..
            }

        }

        return $this->result[0];

    }

    public function getFreeServicePort(){

    }


}
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
        try {
            $this->socket = fsockopen($this->host, 23);
            echo fgets($this->socket);
            fputs($this->socket, "{$this->user}\r\n");
            sleep(1);
            fputs($this->socket, "{$this->pass}\r\n");
            sleep(1);

        } catch (\Exception $e) {
            echo "ERRO: {$e->getMessage()}";
        }


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
        if ($this->socket) {
            $this->comand = fputs($this->socket, "{$comand}\r\n");
            $this->info =  stream_set_timeout($this->socket, 2);
            return $this->comand;
        }
        else
        {
            return false;
        }
    }

    public function doComand($command){

        $comm = $this->executeComand($command);
        $this->closeSocket();
        return $comm;
    }
}
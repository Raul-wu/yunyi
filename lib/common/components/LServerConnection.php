<?php
/**
 * Created by PhpStorm.
 * User: yunteng
 * Date: 5/7/14
 * Time: 1:38 PM
 */

/**
 * LServerConnection represents a connection to a thrift server.
 */

use Thrift\Protocol\TBinaryProtocol;
use Thrift\Transport\TSocket;
//use Thrift\Transport\THttpClient;
//use Thrift\Transport\TBufferedTransport;
use Thrift\Transport\TFramedTransport;

class LServerConnection extends CApplicationComponent
{
	public $clientType = '';
	public $host = '';
	public $port = '';
    public $recvTimeout = 3000;
    public $sendTimeOut = 3000;

	public $transportType = 'TSocket';
	protected $client;
	protected $socket;
	protected $protocol;
	protected $transport;

	public function __construct($clientType = '', $host = '', $port = '')
	{
		$this->clientType = $clientType;
		$this->host = $host;
		$this->port = $port;
	}

	public function getClient($forceCreate = false)
	{
		if ($forceCreate || !$this->client)
		{
            return $this->createClient();
		}

		return $this->client;
	}

	private function createClient()
	{
		$this->socket = new TSocket($this->host, $this->port);
		$this->socket->setRecvTimeout($this->recvTimeout);
		$this->socket->setSendTimeout($this->sendTimeOut);
		//$this->transport = new TFramedTransport($this->socket);
		$this->transport = new \Thrift\Transport\TBufferedTransport($this->socket);
		$this->protocol = new TBinaryProtocol($this->transport);
		$this->client = new $this->clientType($this->protocol);
		$this->transport->open();
        //var_dump('<pre>',$this->client);die;
		return $this->client;
	}

	public function __destruct()
	{
		$this->transport && $this->transport->close();
	}
}

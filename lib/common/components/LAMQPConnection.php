<?php
/**
 * @author: deliliu liuwenjie@e-neway.com
 * @since: 5/8/14 17:02
 */

use PhpAmqpLib\Connection\AMQPConnection;

class LAMQPConnection extends CApplicationComponent
{
	public $host;
	public $port;
	public $user;
	public $password;
	public $vhost;

	/**
	 * @var AMQPConnection
	 */
	private $conn;

	public function __construct($host = "localhost", $port = "5672", $user = "guest", $password = "guest", $vhost = "/")
	{
		$this->host			= $host;
		$this->port			= $port;
		$this->user			= $user;
		$this->password		= $password;
		$this->vhost		= $vhost;
	}

	public function init()
	{
		//php退出前关闭连接
		Yii::app()->attachEventHandler("onEndRequest", array($this, "disconnect"));

		parent::init();
	}

	public function getConnection($forceCreateConnect = false)
	{
		if (!$this->conn || $forceCreateConnect)
		{
			//负责人的说，连接不上会抛异常
			//作者很忙，无心注释，具体异常请查看源代码，但其实业务上不用关心到底抛的是什么异常，若喷请勿带家属
			$this->conn = new AMQPConnection($this->host, $this->port, $this->user, $this->password, $this->vhost);
		}

		return $this->conn;
	}

	/**
	 * @param null|int $channelId null为获取新channel
	 * @param bool $forceCreateConnect
	 * @return \PhpAmqpLib\Channel\AMQPChannel
	 */
	public function getChannel($channelId = null, $forceCreateConnect = false)
	{
		return $this->getConnection($forceCreateConnect)->channel($channelId);
	}

	public function disconnect()
	{
		//[0]其实是connection本身，所以要跳过
		if ($this->conn)
		{
			if (!count($this->conn->channels) > 1)
			{
				/* @var $channel PhpAmqpLib\Channel\AMQPChannel */
				foreach ($this->conn->channels as $n => $channel)
				{
					if ($n === 0) continue;
					$channel->close();
				}
			}

			$this->conn->close();
			$this->conn = null;
		}
	}

	public static function ackMessage($message)
	{
		if (!($message instanceof PhpAmqpLib\Message\AMQPMessage))
		{
			return false;
		}

		$message->delivery_info['channel']
			->basic_ack($message->delivery_info['delivery_tag']);

		return true;
	}

} 
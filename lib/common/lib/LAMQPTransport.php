<?php

/**
 * Created by PhpStorm.
 * User: sangechen
 * Date: 14-5-21
 * Time: 下午2:38
 */
class LAMQPTransport extends \Thrift\Transport\TMemoryBuffer
{
	protected $channel;
	protected $exchangeName;

	const WRITE_MODE = 1;
	const READ_MODE = 2;
	protected $mode;

	protected $rRoutingKey;
	protected $rQueueName;
	protected $rConsumerTag;

	protected $wRoutingKey;

	protected $localReplyTo;
	protected $peerReplyTo; //对方的reply-to

	protected $correlationId;

	private $lastMsg;
	/**
	 * @param $channel \PhpAmqpLib\Channel\AMQPChannel
	 * @param $exchangeName string
	 * @param $routingKey string
	 * @param $queueName string WRITE_MODE~"localReplyTo", READ_MODE~"rQueueName"
	 * @param $defaultMode int
	 */
	public function __construct($channel, $exchangeName, $routingKey,
	                            $defaultMode = self::WRITE_MODE, $queueName = '')
	{
		$this->channel = $channel;
		$this->exchangeName = $exchangeName;
		$this->channel->exchange_declare($this->exchangeName, 'topic', false, true, false); //durable,no-auto-delete

		//由write发起方生成
		$this->correlationId = uniqid();

		//无论读还是写, localReplyTo必须初始化
		$this->localReplyTo = $queueName;
		if (empty($this->localReplyTo)) //可能为空,拼接correlationId
		{
			$this->localReplyTo = 'ReplyTo_' . $this->correlationId;
		}

		//初始化write或read
		if ($defaultMode == self::WRITE_MODE)
		{
			$this->switchToWriteMode($routingKey);
		}
		else
		{
			$this->switchToReadMode($routingKey, $queueName);
		}
	}

	protected function switchToWriteMode($routingKey)
	{
		$this->mode = self::WRITE_MODE;
		$this->wRoutingKey = $routingKey;
	}

	/**
	 * 从write模式切换到read模式
	 * @param $routingKey
	 * @param $queueName
	 */
	protected function switchToReadMode($routingKey, $queueName)
	{
		$this->mode = self::READ_MODE;

		//检测read参数是否变化,重新subscribe()
		if ($this->rRoutingKey != $routingKey || $this->rQueueName != $queueName) {
			$this->rRoutingKey = $routingKey;
			$this->rQueueName = $queueName;
			$this->setupQueue();

			$this->subscribe();
		}
	}

	protected function setupQueue()
	{
		$this->channel->queue_declare($this->rQueueName, false, true, false, false); //durable,no-auto-delete
		$this->channel->queue_bind($this->rQueueName, $this->exchangeName, $this->rRoutingKey);
	}

	protected function subscribe()
	{
		if ($this->rConsumerTag != $this->rQueueName)
		{
			$this->unsubscribe();
			$this->rConsumerTag = $this->rQueueName; //consume the `rQueueName`
			$this->channel->basic_qos(0, 3, false); //read one message at a time
			$this->channel->basic_consume($this->rQueueName, $this->rConsumerTag, false, false, false, false, array($this, 'onDeliver')); //no-ack
		}
		//else already subscribed
	}

	protected function unsubscribe()
	{
		if (!empty($this->rConsumerTag)) //unsubscribe previous consumer
		{
			$this->channel->basic_cancel($this->rConsumerTag);
			$this->rConsumerTag = '';
		}
	}

	/**
	 * 从消息队列中读数据到buffer,
	 *
	 * @param int $len How much to read
	 * @return string The data that has been read
	 * @throws Thrift\Exception\TTransportException if cannot read any more data
	 */
	public function read($len)
	{
		if ($this->available() <= 0) //当前无数据, 需要读msgQ
		{
			if ($this->mode == self::WRITE_MODE) //从写模式转换到读模式{send()后recv()}
			{
				$this->switchToReadMode($this->localReplyTo, $this->localReplyTo);
			}
			//else 继续读模式

			//while($msg == null) {$msg = $this->channel->basic_get($this->rQueueName);}
			while ($this->available() <= 0)
			{
				$this->channel->wait(null, false, 1);
			}
		}

		return parent::read($len);
	}

	/**
	 * @param $msg PhpAmqpLib\Message\AMQPMessage
	 */
	public function onDeliver($msg)
	{
		$this->lastMsg = $msg;
		//var_dump($msg);
		parent::write($msg->body); //var_dump("write: ".$msg->body);
		$this->peerReplyTo = $msg->get('reply_to');
		$this->correlationId = $msg->get('correlation_id');
	}

	public function ack()
	{
		//确认消息处理完毕
		LAMQPConnection::ackMessage($this->lastMsg);
		$this->lastMsg = null;
	}

	/**
	 * Flushes any pending data out of a buffer
	 *
	 * @throws Thrift\Exception\TTransportException if no data in buffer
	 */
	public function flush()
	{
		if ($this->available() <= 0) //当前无数据, 不能flush
		{
			throw new Thrift\Exception\TTransportException(
				'LAMQPTransport: Could not flush! no data in buffer.',
				Thrift\Exception\TTransportException::UNKNOWN);
		}

		if ($this->mode == self::READ_MODE) //从读模式转换到写{recv()后send()}
		{
			$this->switchToWriteMode($this->peerReplyTo);
		}
		//else 继续写模式

		$msg = new PhpAmqpLib\Message\AMQPMessage(parent::getBuffer(),
			array('reply_to' => $this->localReplyTo,
				'correlation_id' => $this->correlationId,
				'delivery_mode' => 2));
		$this->channel->basic_publish($msg, $this->exchangeName, $this->wRoutingKey);
		parent::read(parent::available()); //清空buffer
	}
} 
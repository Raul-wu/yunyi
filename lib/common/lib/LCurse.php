<?php

/**
 * Class LCurse
 *
 * @method bool check()
 * @method mixed curse()
 * @method mixed praise()
 */
abstract class LCurse
{
	const LOG_PREFIX = 'common.lib.LCurse.';
	const MAX_LIFE_TIME = 2592000;

	const MAX_RETRY_LOCK_TIMES = 3;
	const RETRY_LOCK_DELAY = 20000;

	protected static $_instances = array();
	protected static $_keyPrefix = 'LCurse.';
	protected static $_deadlockTimeout = 10;

	protected $_cacheComponent;
	/**
	 * @var Redis
	 */
	protected $_cache;

	/**
	 * @var string
	 */
	protected $_key;

	/**
	 * @var array
	 */
	protected $_options;

	/**
	 * @param array|string $key
	 * @param array $options
	 * @return LCurse
	 */
	public static function getCurse($key, array $options = array())
	{
		$key = is_array($key) ? implode('.', $key) : $key;
		$key = md5($key);
		if (!isset(static::$_instances[$key]) || static::$_instances[$key] === null)
		{
			static::$_instances[$key] = new static($key, $options);
		}

		return static::$_instances[$key];
	}

	/**
	 * @param string $key
	 * @param array $options
	 * @throws CException
	 */
	protected function __construct($key, array $options = array())
	{
		$options['cacheID'] = isset($options['cacheID']) ? $options['cacheID'] : 'cache';
		$this->_options = $options;

		/** @var $cache LRedisCache */
		if (($cache = Yii::app()->getComponent($options['cacheID'])) === null || !$cache instanceof LRedisCache)
		{
			Yii::log("Get LRedisCache component failed: componentId[{$options['cacheID']}]", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
			throw new LException(LError::INTERNAL_ERROR);
		}

		$this->_cacheComponent = $cache;
		$this->_cache = $cache->redis;
		$this->_key = static::$_keyPrefix . $key;

		$retryTimes = 0;
		$locked = false;
		while ($retryTimes++ < self::MAX_RETRY_LOCK_TIMES)
		{
			if ($locked = $this->lock())
			{
				break;
			}
			else
			{
				usleep(self::RETRY_LOCK_DELAY);
			}
		}

		if (!$locked)
		{
			Yii::log("Get lock failed frequency excceded: key[$this->_key]", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
			throw new LException(LError::FREQUENCY_EXCEEDED);
		}

		$this->init();
	}

	function __destruct()
	{
		$this->unlock();
	}

	protected function init()
	{

	}

	/**
	 * check if curse don't set yet
	 * @return mixed
	 */
	abstract protected function checkInternal();

	/**
	 * @return mixed
	 */
	abstract protected function curseInternal();

	/**
	 * @return mixed
	 */
	abstract protected function praiseInternal();

	/**
	 * Delegate to corresponding internal method with before and after hook.
	 * @param $name
	 * @param $args
	 * @throws CException
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		if (method_exists($this, "{$name}Internal"))
		{
			return call_user_func_array(array($this, "{$name}Internal"), $args);
		}
		else
		{
			Yii::log("Method does not exist: method[$name]", CLogger::LEVEL_ERROR, self::LOG_PREFIX . $name);
			throw new LException(LError::INTERNAL_ERROR);
		}
	}

	/**
	 * @return bool
	 */
	protected function lock()
	{
		$lock = $this->_cache->set($this->getLockKey(), time() + static::$_deadlockTimeout, array('nx'));
		if (!$lock)
		{
			$expire = $this->_cache->get($this->getLockKey());
			if ($expire >= time())	// check whether deadlock happened!
			{
				return false;
			}
			else
			{
				$expireNow = $this->_cache->getSet($this->getLockKey(), time() + static::$_deadlockTimeout);
				if ($expire != $expireNow)
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	 *
	 */
	protected function unlock()
	{
		$expire = $this->_cache->get($this->getLockKey());
		if ($expire && $expire >= time())
		{
			$this->_cache->del($this->getLockKey());
		}
	}

	protected function getKey()
	{
		return $this->_cacheComponent->keyPrefix . $this->_key;
	}

	/**
	 * @return string
	 */
	protected function getLockKey()
	{
		return $this->getKey() . '_lock';
	}
}
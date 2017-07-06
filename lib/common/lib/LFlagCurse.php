<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-5-5
 * Time: PM1:58
 */

class LFlagCurse extends LCurse
{
	protected static $_keyPrefix = 'LFlagCurse.';

	protected function checkInternal()
	{
		$ttl = $this->_cache->ttl($this->getKey());
		if ($ttl === -2)
		{
			return 0;
		}
		else
		{
			return $ttl;
		}
	}

	protected function curseInternal($expire = self::MAX_LIFE_TIME)
	{
		$expire = is_int($expire) ? $expire : self::MAX_LIFE_TIME;
		return $this->_cache->set($this->getKey(), time() + $expire, $expire);
	}

	protected function praiseInternal()
	{
		$this->_cache->del($this->getKey());
	}
}
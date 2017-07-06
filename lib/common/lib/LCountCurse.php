<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-5-5
 * Time: PM2:05
 */

class LCountCurse extends LCurse
{
	protected static $_keyPrefix = 'LCountCurse.';

	protected function checkInternal($target = 0)
	{
		if (($value = $this->_cache->get($this->getKey())) === false || $value < $target)
		{
			if (!$value)
			{
				$value = 0;
			}
			return $target - $value;
		}
		else
		{
			return 0;
		}
	}

	protected function curseInternal()
	{
		$result = $this->_cache->incr($this->getKey());
		$this->_cache->expire($this->getKey(), self::MAX_LIFE_TIME);

		return $result;
	}

	protected function praiseInternal($reset = false)
	{
		if ($reset)
		{
			$this->_cache->del($this->getKey());
		}
		else
		{
			$this->_cache->decr($this->getKey());
		}
	}
}
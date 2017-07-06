<?php
/**
 * @author: deliliu liuwenjie@e-neway.com
 * @since: 5/7/14 21:13
 */

class LNetLogRoute extends CLogRoute
{

	static $_begins = array();
	/**
	 * Processes log messages and sends them to specific destination.
	 * Derived child classes must implement this method.
	 * @param array $logs list of messages. Each array element represents one message
	 * with the following structure:
	 * array(
	 *   [0] => message (string)
	 *   [1] => level (string)
	 *   [2] => category (string)
	 *   [3] => timestamp (float, obtained by microtime(true));
	 */
	protected function processLogs($logs)
	{
		if (ENVIRONMENT == ENVIRONMENT_DEV || ENVIRONMENT == ENVIRONMENT_TEST)
		{
			$oldMask = @umask(0);
		}

		foreach ($logs as $log)
		{
			list($msg, $level, $category, $time) = $log;

			if ($level == CLogger::LEVEL_TRACE && strpos($category, "system") === 0)
			{
				continue;
			}

			list($app) = explode(".", $category, 2);

			if ($level == CLogger::LEVEL_PROFILE)
			{
				if (($begin = strpos($msg, 'begin:')) !== false)
				{
					$key = $category . '.' . substr($msg, $begin + 6);
					if (!isset(self::$_begins[$key]))
					{
						self::$_begins[$key] = array();
					}
					array_push(self::$_begins[$key], $log);
					continue;
				}

				if (($end = strpos($msg, 'end:')) === false)
				{
					continue;
				}

				$key = $category . '.' . substr($msg, $end + 4);
				if (!isset(self::$_begins[$key]) || !($beginLog = array_pop(self::$_begins[$key])))
				{
					continue;
				}

				if ($beginLog && isset($beginLog[3]))
				{
					$beginTime = $beginLog[3];
					$app = 'common';
					$category = 'profile';
					$msg = sprintf("%s execute time microsecond[%d]", $msg, ($time - $beginTime) * 1000);
				}
				else
				{
					continue;
				}
			}

			$path = "/data/logs/{$app}";
			if (!file_exists($path) && !mkdir($path))
			{
				file_put_contents("/tmp/LnetLogRoute.log", "can\'t create folder: {$path}", FILE_APPEND);
				return;
			}

			$msg = sprintf("%s %s svrIp[%s] %s\n",
					date("Y-m-d H:i:s", $time) . "." . sprintf("%03d", ($time - floor($time)) * 1000),
					$category,
                    LUtil::svrIp(),
					$msg
				);

			$fileName = date("Ymd000_") . $level . ".log";

			file_put_contents("{$path}/{$fileName}", $msg, FILE_APPEND);
		}

		if (ENVIRONMENT == ENVIRONMENT_DEV || ENVIRONMENT == ENVIRONMENT_TEST)
		{
			@umask($oldMask);
		}
	}
}
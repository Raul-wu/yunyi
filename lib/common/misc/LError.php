<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-4-21
 * Time: PM5:12
 */

class LError
{
	const SUCCESS = 0;
    const NOT_FOUND = 404;
	// 1000 系统公用段
	const PARAM_ERROR = 100001;
	const NO_LOGIN = 100002;
	const INVALID_CSRF_TOKEN = 100003;
	const INTERNAL_ERROR = 100004;
	const FREQUENCY_EXCEEDED = 100005;
	const INVALID_OP = 100006;
	const CAPTCHA_INVALID = 100007;
	const CAPTCHA_NEEDED = 100008;
	const MOBILE_TOKEN_INVALID = 100009;
	const MULTIPLE_LOGIN = 100010;
	const NO_PERMISSION = 100011;
	const NO_BUY_PERMISSION = 100012;
	// END 系统公用段

	public static function getErrMsg($message, array $params = array())
	{
		$patterns = array_map(function($pattern) {
				return "/#$pattern#/";
			}, array_keys($params));
		$values = array_values($params);
		return preg_replace($patterns, $values, $message);
	}

	public static function getErrMsgByCode($code, array $params = array())
	{
		$errMsg = static::errorMsg();
		$message = isset($errMsg[$code]) ? $errMsg[$code] : '服务器忙，请稍后再试～';
		return self::getErrMsg($message, $params);
	}

	public static function errorMsg()
	{
		return self::$errMsg;
	}

	/**
	 * 合并错误码数组，保证第一个数组会被后面的数组覆盖
	 * @param array $errMsg
	 * @param array $extendMsg
	 * @return array
	 */
	public static function mergeErrorMsg($errMsg, $extendMsg)
	{
		$args=func_get_args();
		$res=array_shift($args);
		while(!empty($args))
		{
			$next=array_shift($args);
			foreach($next as $k => $v)
			{
				if(is_array($v) && isset($res[$k]) && is_array($res[$k]))
					$res[$k]=self::mergeErrorMsg($res[$k],$v);
				else
					$res[$k]=$v;
			}
		}
		return $res;
	}
}
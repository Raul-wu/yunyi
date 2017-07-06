<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-5-5
 * Time: PM6:01
 */

class LException extends CException
{
	public function __construct($code = LError::INTERNAL_ERROR, $message = array())
	{
		if (is_array($message))
		{
			$message = LError::getErrMsgByCode($code, $message);
		}
		parent::__construct($message, $code);
	}
}
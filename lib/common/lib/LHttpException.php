<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14/12/17
 * Time: PM2:41
 */

class LHttpException extends CHttpException
{
	public function __construct($code = LError::INTERNAL_ERROR, $message = array(), $status = 404)
	{
		if (is_array($message))
		{
			$message = LError::getErrMsgByCode($code, $message);
		}
		parent::__construct($status, $message, $code);
	}

} 
<?php

/**
 * Class LController
 *
 */
abstract class LController extends CController
{

	public function actions()
	{
		return array(
			'captcha' => Yii::app()->params['captcha'],
			'captcha2' => Yii::app()->params['captcha2'],
		);
	}

	/**
	 * @param mixed $result
	 */
	public function ajaxResponse($result = array())
	{
		$callback = Yii::app()->request->getQuery('callback');
		if (empty($result))
		{
			$result = new stdClass();
		}
		if ($callback && is_string($callback) && preg_match('/^[0-9A-Za-z_]+$/', $callback))
		{
			header('Content-type: application/javascript');
			echo ('try{' . $callback . '(' . json_encode($result) . ');}catch(e){}');
		}
		else
		{
			header('Content-type: application/json');
			echo json_encode($result);
		}
		Yii::app()->end();
	}

	/**
	 * @param array $data
	 * @param string $html
	 */
	public function ajaxSuccess(array $data = array(), $html = '')
	{
		$this->ajaxReturn(LError::SUCCESS, '', $data, $html);
	}

	/**
	 * @param int $code
	 * @param array|string $msg
	 * @param array $data
	 * @param string $html
	 */
	public function ajaxReturn($code = LError::SUCCESS, $msg = array(), array $data = array(), $html = '')
	{
		if (is_array($msg))
		{
			$msg = LError::getErrMsgByCode($code, $msg);
		}
		if (empty($data))
		{
			$data = new stdClass();
		}
		$this->ajaxResponse(array(
				'retCode' => $code,
				'retMsg' => $msg,
				'retData' => $data,
				'retHtml' => $html,
			));
	}

	public function ajaxError(array $data = array(), $html = '') {
        $this->ajaxReturn(LError::PARAM_ERROR, '', $data, $html);
    }
}
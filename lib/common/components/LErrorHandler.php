<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-5-5
 * Time: PM5:43
 */

class LErrorHandler extends CErrorHandler
{
	protected $_error;

	protected function handleException($exception)
	{
		$data = $this->formatException($exception);
		if (Yii::app()->request->isAjaxRequest)
		{
			/** @var $app CWebApplication */
			$app = Yii::app();
			/** @var $controller LController */
			$controller = $app->getController();
			if (!$controller instanceof LController)
			{
				$controller = $app->createController('/');
				$controller = $controller[0];
			}
			$controller->ajaxReturn($data['errorCode'] ? $data['errorCode'] : $data['code'], YII_DEBUG ? $data['message'] : array(), YII_DEBUG ? $data : array());
		}
		else
		{
			if (!headers_sent())
			{
				header("HTTP/1.0 {$data['code']} " . $this->getHttpHeader($data['code'], get_class($exception)));
			}

			if ($exception instanceof CHttpException || !YII_DEBUG)
			{
				$this->render('error', $data);
			}
			else
			{
				$this->render('exception', $data);
			}
		}
	}

	protected function handleError($event)
	{
		$data = $this->formatError($event);
		if (Yii::app()->request->isAjaxRequest)
		{
			/** @var $app CWebApplication */
			$app = Yii::app();
			/** @var $controller LController */
			$controller = $app->getController();
			if (!$controller instanceof LController)
			{
				$controller = $app->createController('/');
				$controller = $controller[0];
			}
			$controller->ajaxReturn(LError::INTERNAL_ERROR, YII_DEBUG ? $data['message'] : array(), YII_DEBUG ? $data : array());
		}
		else
		{
			if (!headers_sent())
			{
				header("HTTP/1.0 500 Internal Server Error");
			}
			if (YII_DEBUG)
			{
				$this->render('exception', $data);
			}
			else
			{
				$this->render('error', $data);
			}
		}
	}

	protected function formatException(Exception $exception)
	{
		if (($trace = $this->getExactTrace($exception)) === null)
		{
			$fileName = $exception->getFile();
			$errorLine = $exception->getLine();
		}
		else
		{
			$fileName = $trace['file'];
			$errorLine = $trace['line'];
		}

		$trace = $exception->getTrace();

		foreach ($trace as $i => $t)
		{
			if (!isset($t['file']))
			{
				$trace[$i]['file'] = 'unknown';
			}

			if (!isset($t['line']))
			{
				$trace[$i]['line'] = 0;
			}

			if (!isset($t['function']))
			{
				$trace[$i]['function'] = 'unknown';
			}

			unset($trace[$i]['object']);
		}

		return $this->_error = array(
			'code' => ($exception instanceof CHttpException) ? $exception->statusCode : 500,
			'type' => get_class($exception),
			'errorCode' => $exception->getCode(),
			'message' => $exception->getMessage(),
			'file' => $fileName,
			'line' => $errorLine,
			'trace' => $exception->getTraceAsString(),
			'traces' => $trace,
		);
	}

	protected function formatError(CErrorEvent $event)
	{
		$trace = debug_backtrace();
		// skip the first 3 stacks as they do not tell the error position
		if (count($trace) > 3)
		{
			$trace = array_slice($trace, 3);
		}
		$traceString = '';
		foreach ($trace as $i => $t)
		{
			if (!isset($t['file']))
			{
				$trace[$i]['file'] = 'unknown';
			}

			if (!isset($t['line']))
			{
				$trace[$i]['line'] = 0;
			}

			if (!isset($t['function']))
			{
				$trace[$i]['function'] = 'unknown';
			}

			$traceString .= "#$i {$trace[$i]['file']}({$trace[$i]['line']}): ";
			if (isset($t['object']) && is_object($t['object']))
			{
				$traceString .= get_class($t['object']) . '->';
			}
			$traceString .= "{$trace[$i]['function']}()\n";

			unset($trace[$i]['object']);
		}

		switch ($event->code)
		{
			case E_WARNING:
				$type = 'PHP warning';
				break;
			case E_NOTICE:
				$type = 'PHP notice';
				break;
			case E_USER_ERROR:
				$type = 'User error';
				break;
			case E_USER_WARNING:
				$type = 'User warning';
				break;
			case E_USER_NOTICE:
				$type = 'User notice';
				break;
			case E_RECOVERABLE_ERROR:
				$type = 'Recoverable error';
				break;
			default:
				$type = 'PHP error';
		}
		return $this->_error = array(
			'code' => 500,
			'type' => $type,
			'message' => $event->message,
			'file' => $event->file,
			'line' => $event->line,
			'trace' => $traceString,
			'traces' => $trace,
		);
	}

	public function getError()
	{
		return $this->_error;
	}
}
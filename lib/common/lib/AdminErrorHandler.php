<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-28
 * Time: 下午1:44
 */
class AdminErrorHandler extends LErrorHandler
{
    //自定义错误页面
    public $exceptionAction;

    protected function handleException(Exception $exception)
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

    /**
     * 重写错误信息
     * @param string $view
     * @param array $data
     */
    protected function render($view,$data)
    {
        if( $view==='error' && $this->errorAction !== null )
        {
            Yii::app()->runController($this->errorAction);
        }
        elseif( $view==='exception' && $this->exceptionAction !== null )
        {
            Yii::app()->runController($this->exceptionAction);
        }
        else
        {
            // additional information to be passed to view
            $data['version']=$this->getVersionInfo();
            $data['time']=time();
            $data['admin']=$this->adminInfo;
            include($this->getViewFile($view,$data['code']));
        }
    }
}
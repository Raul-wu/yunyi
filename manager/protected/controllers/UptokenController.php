<?php

class UptokenController extends AdminBaseController
{
    public function actionIndex()
    {
        /* @var $img LQiniuUpToken */
        $img = Yii::app()->imgUpToken;

        $key = Yii::app()->request->getPost("key");
        if (!empty($key)) {
            $img->insertOnly = 0;
            $img->scope .= ":$key";
        }

        echo json_encode(array("uptoken" => $img->Token()));
    }

    public function actionImgPdf()
    {
        $file = Yii::app()->fileUpToken;

        echo json_encode(array("uptoken" => $file->Token()));
    }

    public function actionPrivate()
    {
        $privateImg = Yii::app()->privateImgUpToken;

        echo json_encode(array("uptoken" => $privateImg->Token()));
    }

    public function actionPrivateExcel()
    {
        $privateImg = Yii::app()->privateExcelUpToken;

        echo json_encode(array("uptoken" => $privateImg->Token()));
    }

    public function actionPrivateFile()
    {
        if ($url = Yii::app()->request->getParam('url')) {
            /* @var $privateImg LQiniuUpToken */
            $privateImg = Yii::app()->fileUpToken;

            $this->render('privateFile', array(
                'url' => $privateImg->getPrivateToken($url, 60)
            ));
        }
    }

    public static function actionFile()
    {
        $url = Yii::app()->request->getQuery('url');
        $file = new SplFileInfo($url);
        $ext = $file->getExtension();

        if (in_array(strtolower($ext), ['jpg', 'jpeg', 'gif', 'png'])) {
            header("content-type: image/png");
        } else {
            header("Content-type: application/octet-stream;charset=utf-8");
            header("Content-Disposition: attachment; filename=" . md5($url) . '.' . $ext);
        }

        $fileUpload = Yii::app()->fileUpToken;
        $url = $fileUpload->getPrivateToken($url);

        $data = '';
        $boundary = "---------------------" . substr(md5(rand(0, 32000)), 0, 10);

        $data .= "--$boundary\n";

        $params = array('http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: multipart/form-data; boundary=' . $boundary,
            'content' => $data,
        ));
        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            throw new Exception("Problem with $url, $php_errormsg");
        }
        $response = @stream_get_contents($fp);

        echo $response;
    }

    /**
     * FTP文件下载
     */
    public function actionFtpDownFile()
    {
        $file_path = Yii::app()->request->getParam("file_path");

        if(empty($file_path))
        {
            throw new CHttpException(404, '你访问的页面不存在');
        }

        $filename = basename($file_path);

        $ftp = Yii::app()->ftp;
        $tmpfile = tempnam( getcwd()."/", "temp" );
        $ftp->get($tmpfile, $file_path, FTP_BINARY);
        header("Content-Type:application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $filename);
        readfile($tmpfile);
        unlink($tmpfile); // 删除临时文件
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 14-7-4
 * Time: 上午10:48
 * Class LQiniuUpService
 * @property string $scope 指定上传的目标资源空间（Bucket）和资源名（Key）<bucket>或者<bucket>:<key>;
 * @property int $deadline 上传请求授权的截止时间;
 * @property int $insertOnly 限定为“新增”语意;
 * @property string $saveKey  自定义资源名;
 * @property string $endUser 唯一属主标识;
 * @property string $returnUrl Web端文件上传成功后，浏览器执行303跳转的URL;
 * @property string $returnBody 使用json格式的魔法变量和自定义变量;
 * @property string $callbackUrl 上传成功后七牛POST回调URL;
 * @property string $callbackBody 使用url query string格式的魔法变量和自定义变量;
 * @property string $persistentOps 资源上传成功后触发执行的预转持久化处理指令列表;
 * @property string $persistentNotifyUrl 接收预转持久化结果通知的URL;
 * @property int $fsizeLimit 限定上传文件的大小;
 * @property int $detectMime 开启MimeType侦测功能;
 * @property string $mimeLimit 限定用户上传的文件类型;
 */
class LQiniuUpToken extends CApplicationComponent{
	public $scope;
	public $deadline;
	public $insertOnly;
	public $saveKey;
	public $endUser;
	public $returnUrl;
	public $returnBody;
	public $callbackUrl;
	public $callbackBody;
	public $persistentOps;
	public $persistentNotifyUrl;
	public $fsizeLimit;
	public $detectMime;
	public $mimeLimit;

	public $SecretKey;
	public $AccessKey;

	public $basePath;

	public function Token()
	{
		$deadline = $this->deadline;
		if ($deadline == 0)
		{
			$deadline = 3600;
		}

		$deadline += time();

		$policy = array(
			'scope' => $this->scope, 
			'deadline' => $deadline
		);
		
		if (!empty($this->callbackUrl))
		{
			$policy['callbackUrl'] = $this->callbackUrl;
		}

		if (!empty($this->callbackBody))
		{
			$policy['callbackBody'] = $this->callbackBody;
		}

		if (!empty($this->returnUrl))
		{
			$policy['returnUrl'] = $this->returnUrl;
		}

		if (!empty($this->returnBody))
		{
			$policy['returnBody'] = $this->returnBody;
		}

		if (!empty($this->endUser))
		{
			$policy['endUser'] = $this->endUser;
		}

		if (!empty($this->saveKey)) {
			$policy['saveKey'] = $this->saveKey;
		}

		if (!empty($this->fsizeLimit))
		{
			$policy['fsizeLimit'] = $this->fsizeLimit;
		}

		if (!empty($this->mimeLimit))
		{
			$policy['mimeLimit'] = $this->mimeLimit;
		}

		if (!empty($this->detectMime))
		{
			$policy['detectMime'] = $this->detectMime;
		}

		if (!empty($this->insertOnly))
		{
			$policy['insertOnly'] = $this->insertOnly;
		}

		if (!empty($this->persistentOps))
		{
			$policy['persistentOps'] = $this->persistentOps;
		}

		if (!empty($this->persistentNotifyUrl))
		{
			$policy['persistentNotifyUrl'] = $this->persistentNotifyUrl;
		}

		$b = json_encode($policy);

		return $this->signWithData($b);
	}

	public function getPrivateToken($url, $timeout = 10)
	{
		$urlToCode = $url . '?e=' . (time() + $timeout);
		return $urlToCode . '&token=' . $this->sign($urlToCode);
	}

	public function urlSafeBase64Encode($str)
	{
		$find = array('+', '/');
		$replace = array('-', '_');
		return str_replace($find, $replace, base64_encode($str));
	}

	public function urlSafeBase64Decode($str)
	{
		$find = array('_', '-');
		$replace = array('/', '+');
		return base64_decode(str_replace($find, $replace, $str));
	}

	private function signWithData($data)
	{
		$data = $this->urlSafeBase64Encode($data);
		return $this->sign($data) . ':' . $data;
	}

	private function sign($data)
	{
		$sign = hash_hmac('sha1', $data, $this->SecretKey, true);
		return $this->AccessKey . ':' . $this->urlSafeBase64Encode($sign);
	}
} 
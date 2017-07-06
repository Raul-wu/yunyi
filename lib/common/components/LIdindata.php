<?php


class LIdindata extends CApplicationComponent
{
	const LOG_PREFIX = 'common.components.LIdindata.';

	public $urlPrefix;
	public $userId;
	public $md5Key;
	public $desKey;
	public $proxy;
	public $timeout;

	public function auth($name, $idCardNo)
	{
		$coopOrderNo = time() . mt_rand(0, 1000);
		$params = array(
			'userId' => $this->userId,
			'coopOrderNo' => $coopOrderNo,
			'auName' => $name,
			'auId' => $idCardNo,
			'ts' => time(),
		);

		$str = '';
		$sign = '';
		foreach ($params as $k => $v)
		{
			if (empty($v))
			{
				continue;
			}
			$str .= $k . $v;
			$sign = md5($str . $this->md5Key);
		}

		$cipher = LAESHelper::getInstance()->initCipher('DES-ECB', "")->initKey($this->desKey);
		$params['auName'] = bin2hex($cipher->encrypt($params['auName'], true));
		$params['auId'] = bin2hex($cipher->encrypt($params['auId'], true));
		$params['reqDate'] = date("Y-m-d H:i:s");
		$params['sign'] = $sign;

		$client = new LHttpClient();
		$client->setGateURL($this->urlPrefix . '/spAuthenInfoApi.htm');
		$queryString = http_build_query($params, null, '&', PHP_QUERY_RFC3986);

		$client->setQueryString($queryString);
		if ($this->proxy)
		{
			$client->setProxy($this->proxy);
		}
		if ($this->timeout)
		{
			$client->setTimeout($this->timeout);
		}

		Yii::log("Call idindata. requestUrl[{$client->getRequestURL()}]", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);

		Yii::beginProfile('Call idindata auth', self::LOG_PREFIX . __FUNCTION__);
		$ret = $client->execute();
		Yii::endProfile('Call idindata auth', self::LOG_PREFIX . __FUNCTION__);

		if ($ret)
		{
			$response = $client->getResponseBody();
			$xml = simplexml_load_string($response);
			if ($xml === false)
			{
				Yii::log("Parse response failed. coopOrderNo[{$coopOrderNo}] response[{$response}]", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
				return false;
			}

			$retCoopOrderNo = $xml->coopOrderNo;
			$auOrderNo = $xml->auOrderNo;
			$resultCode = $xml->auResultCode;
			$resultMsg = $xml->auResultInfo;

			if ($resultCode == 'SUCCESS')
			{
				$auSuccessTime = $xml->auSuccessTime;
				Yii::log("Auth succ. coopOrderNo[{$coopOrderNo}] retCoopOrderNo[{$retCoopOrderNo}] auOrderNo[{$auOrderNo}] auSuccessTime[{$auSuccessTime}]", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
				return 1;
			}
			else
			{
				$errNo = $xml->errorCode;
				$errMsg = $xml->errorMsg;
				Yii::log("Auth failed. coopOrderNo[{$coopOrderNo}] retCoopOrderNo[{$retCoopOrderNo}] auOrderNo[{$auOrderNo}] errNo[{$errNo}] errMsg[{$errMsg}] resultCode[{$resultCode}] resultMsg[{$resultMsg}]", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
				return 2;
			}
		}
		else
		{
			$errNo = $client->getCurlErrno();
			$errMsg = $client->getCurlErrMsg();
			Yii::log("Call Idindata auth failed. coopOrderNo[{$coopOrderNo}] errNo[{$errNo}] errMsg[{$errMsg}]", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
			return false;
		}
	}
}

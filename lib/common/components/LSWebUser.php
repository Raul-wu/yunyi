<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-4-18
 * Time: AM11:25
 */

class LSWebUser extends LWebUser
{
	const LOG_PREFIX = 'service.user.common.components.';
    
    private static $_client;
    /** @var $_userInfo LSUser */
    private static $_userInfo;

	/**
	 * @return mixed
	 */
    private static function getClient()
    {
        if (!static::$_client)
        {
            static::$_client = Yii::app()->loginServer->getClient();
        }
        return static::$_client;
    }
    
    public function __construct()
    {
         static::$_userInfo = new LSUser();
    }
    
    public function getLoginKey()
    {
        $loginKey = static::$_userInfo->get('loginKey');
        if (!$loginKey) 
        {
            $loginKey = parent::getLoginKey();
            static::$_userInfo->set('loginKey', $loginKey);
            return $loginKey;
        }
        
        return $loginKey;
    }
    
	public function getIsGuest()
	{
        $cachedUid = static::$_userInfo->get('uid');
        if ($cachedUid !== null)
        {
            return false; 
        }
		$loginKey = $this->getLoginKey();
		if ($loginKey)
		{
			$sessionInfo = $this->getLoginStatus($loginKey);
			if ($sessionInfo && !empty($sessionInfo->uid))
			{
				return false;
			}
			return true;
		}
		else
		{
			//为了兼容老的登录态设置
			return $this->getState('__id')===null;
		}
	}

	public function getId()
	{
        $cachedUid = static::$_userInfo->get('uid');
        if ($cachedUid !== null)
        {
            return $cachedUid; 
        }
        
		$loginKey = $this->getLoginKey();
		if ($loginKey)
		{
			$sessionInfo = $this->getLoginStatus($loginKey);
			if ($sessionInfo && !empty($sessionInfo->uid))
			{
				return $sessionInfo->uid;
			}
			return null;
		}
		return parent::getId();
	}

	public function getName()
	{
        $cachedName = static::$_userInfo->get('name');
        if ($cachedName !== null) 
        {
            return $cachedName;
        }
		$loginKey = $this->getLoginKey();
		if ($loginKey)
		{
			$sessionInfo = $this->getLoginStatus($loginKey);
			if ($sessionInfo && !empty($sessionInfo->name))
			{
				return $sessionInfo->name;
			}
			$CWebUser = new CWebUser();
			static::$_userInfo->set('name', $CWebUser->guestName);
			return $CWebUser->guestName;
		}
		return parent::getName();
	}

	public function getCorpId()
	{
        $cachedCorpId = static::$_userInfo->get('corpId');
        if ($cachedCorpId !== null)
        {
            return $cachedCorpId;
        }
        
		$loginKey = $this->getLoginKey();
		if ($loginKey)
		{
			$sessionInfo = $this->getLoginStatus($loginKey);
			if ($sessionInfo && !empty($sessionInfo->corpId))
			{
				return $sessionInfo->corpId;
			}
		}
		
		return parent::getCorpId();
	}

	public function setIsWeb()
	{
		return $this->setUserProperties(array('isWeb' => true));
	}

	public function isWeb()
	{
        $cachedIsWeb = static::$_userInfo->get('isWeb');
        if ($cachedIsWeb !== null)
        {
            return $cachedIsWeb;
        }
        
		$loginKey = $this->getLoginKey();
		if ($loginKey)
		{
			$sessionInfo = $this->getLoginStatus($loginKey);
			if ($sessionInfo !== null && !empty($sessionInfo->isWeb))
			{
				return $sessionInfo->isWeb;
			}
		}
		return null;
	}

	/**
	 * @param $user
	 * @param array $extraParams
	 */
	public static function setUserInfoStaticCache($user, $extraParams = array())
	{
		if (is_object($user))
		{
			$user = get_object_vars($user);
		}
		if (is_array($user) && !empty($extraParams))
		{
			$user = array_merge($user, $extraParams);
		}

		static::$_userInfo->setUserInfo($user);
	}
	
	public function setCorpId($corpId)
	{
		return $this->setUserProperties(array('corpId' => $corpId));
	}
	
	private function setUserProperties($userProperties)
	{
		$loginKey = $this->getLoginKey();
		if ($loginKey)
		{
			try
			{
				$client = static::getClient();
				$req = new \service\login\SetUserPropertiesReq();
				$req->loginKey = $loginKey;
				if (is_array($userProperties) && !empty($userProperties))
				{
					foreach ($userProperties as $proName => $proValue)
					{
						$req->userProperties[] = new \service\base\KV(array('k' => $proName, 'v' => $proValue));
					}
				}
				/** @var \service\login\SetUserPropertiesResp $resp */
				$resp = $client->setUserProperties($req);
				if (!empty($resp) && $resp->result == true)
				{
					foreach ($userProperties as $proName => $proValue)
					{
						static::$_userInfo->set($proName , $proValue);
					}
				}
				return true;
			}
			catch (\service\exception\BaseException $e)
			{
				Yii::log("UserServerException: retcode[{$e->retcode}] retmsg[{$e->retmsg}]",
					CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
			}
			catch (\Thrift\Exception\TException $e)
			{
				Yii::log("TException:  msg[{" . $e->getCode() . ":" . $e->getMessage() . "}]",
					CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
			}
		}
		
		return false;
	}

	/**
	 * @param $loginKey
	 * @return \service\login\LoginKeyValue | null
	 */
	private function getLoginStatus($loginKey)
	{
		try
		{
			$client = static::getClient();
			$req = new \service\login\LoginStatusReq(array('loginKey' => $loginKey));
			/** @var \service\login\LoginStatusResp $resp */
			$resp = $client->getLoginStatus($req);
			if (!empty($resp))
			{
				static::$_userInfo->setUserInfo($resp->sessionInfo);
				return $resp->sessionInfo;
			}
		}
		catch (\service\exception\BaseException $e)
		{
			Yii::log("UserServerException: retcode[{$e->retcode}] retmsg[{$e->retmsg}]",
				CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
		}
		catch (\Thrift\Exception\TException $e)
		{
			Yii::log("TException:  msg[{" . $e->getCode() . ":" . $e->getMessage() . "}]",
				CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
		}

		return null;
	}
}
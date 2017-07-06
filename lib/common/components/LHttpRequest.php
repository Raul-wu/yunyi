<?php
/**
 * Created by PhpStorm.
 * User: soulwu
 * Date: 14-4-29
 * Time: AM11:12
 */

class LHttpRequest extends CHttpRequest
{
	const LOG_PREFIX = 'common.components.LHttpRequest.';

	const TYPE_STRING = 1;
	const TYPE_INTEGER = 2;
	const TYPE_ARRAY = 3;

	public $noCsrfValidationRoutes = array();

	protected function normalizeRequest()
	{
		parent::normalizeRequest();
		if ($this->enableCsrfValidation)
		{
			$url = Yii::app()->getUrlManager()->parseUrl($this);
			foreach ($this->noCsrfValidationRoutes as $route)
			{
			    if (strpos($url,$route) === 0)
				{
					Yii::app()->detachEventHandler('onBeginRequest', array($this,'validateCsrfToken'));
					break;
				}
			}
		}
		if(strpos(Yii::app()->getUrlManager()->parseUrl($this), 'h5') === 0) {
            Yii::app()->detachEventHandler('onBeginRequest', array($this,'validateCsrfToken'));
        }
	}

	public function getIsAjaxRequest()
	{
		return parent::getIsAjaxRequest() || $this->getParam('ajax');
	}

	public function validateCsrfToken($event)
	{
		if ($this->getIsAjaxRequest())
		{
			$cookies = $this->getCookies();
			$userToken = $this->getParam($this->csrfTokenName);
			$cookieToken = '';
			if (!empty($userToken) && $cookies->contains($this->csrfTokenName))
			{
				$cookieToken = $cookies->itemAt($this->csrfTokenName)->value;
				$valid= $cookieToken === $userToken;
			}
			else
			{
				$valid = false;
			}
			if (!$valid)
			{
				Yii::log("Bad request: userToken[$userToken] cookieToken[$cookieToken]", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
				throw new LException(LError::INVALID_CSRF_TOKEN);
			}
		}
		else
		{
			parent::validateCsrfToken($event);
		}
	}

	public function getUserHostAddress()
	{
		if (!isset($_SERVER['REMOTE_ADDR']) || LUtil::isLAN($_SERVER['REMOTE_ADDR']))
		{
			if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
			{
				list($_SERVER["REMOTE_ADDR"]) = explode(',', $_SERVER["HTTP_X_FORWARDED_FOR"]);
				$_SERVER["REMOTE_ADDR"] = trim($_SERVER["REMOTE_ADDR"]);
			}
		}

		return $_SERVER["REMOTE_ADDR"];
	}

	/**
	 * Returns the named GET or POST parameter value.
	 * If the GET or POST parameter does not exist, the second parameter to this method will be returned.
	 * If both GET and POST contains such a named parameter, the GET parameter takes precedence.
	 * @param string $name the GET parameter name
	 * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
	 * @param int $type
	 * @return mixed the GET parameter value
	 * @see getQuery
	 * @see getPost
	 */
	public function getParam($name, $defaultValue=null, $type = self::TYPE_STRING)
	{
		if (isset($_GET[$name]))
		{
			return $this->getQuery($name, $defaultValue, $type);
		}
		else if (isset($_POST[$name]))
		{
			return $this->getPost($name, $defaultValue, $type);
		}
		return $defaultValue;
	}

	/**
	 * Returns the named GET parameter value.
	 * If the GET parameter does not exist, the second parameter to this method will be returned.
	 * @param string $name the GET parameter name
	 * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
	 * @param int $type
	 * @return mixed the GET parameter value
	 * @see getPost
	 * @see getParam
	 */
	public function getQuery($name, $defaultValue=null, $type = self::TYPE_STRING)
	{
		if (isset($_GET[$name]))
		{
			switch ($type)
			{
				case self::TYPE_STRING:
					return is_array($_GET[$name]) ? $defaultValue : trim(strval($_GET[$name]));
				case self::TYPE_INTEGER:
					return is_array($_GET[$name]) ? $defaultValue : intval($_GET[$name]);
				default:
					return $_GET[$name];
			}
		}
		return $defaultValue;
	}

	/**
	 * Returns the named POST parameter value.
	 * If the POST parameter does not exist, the second parameter to this method will be returned.
	 * @param string $name the POST parameter name
	 * @param mixed $defaultValue the default parameter value if the POST parameter does not exist.
	 * @param int $type
	 * @return mixed the POST parameter value
	 * @see getParam
	 * @see getQuery
	 */
	public function getPost($name, $defaultValue=null, $type = self::TYPE_STRING)
	{
		if (isset($_POST[$name]))
		{
			switch ($type)
			{
				case self::TYPE_STRING:
					return is_array($_POST[$name]) ? $defaultValue : trim(strval($_POST[$name]));
				case self::TYPE_INTEGER:
					return is_array($_POST[$name]) ? $defaultValue : intval($_POST[$name]);
				default:
					return $_POST[$name];
			}
		}
		return $defaultValue;
	}
}
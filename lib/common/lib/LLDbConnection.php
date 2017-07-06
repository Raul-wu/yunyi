<?php
/**
 * Created by PhpStorm.
 * User: wlt7008
 * Date: 2015/7/29
 * Time: 15:15
 *
 *
 * mysql长链接断掉重连
 * */
class LLDbConnection extends CDbConnection
{
	public $_connect = false;

	public $_pdo = null;

	const LOG_PREFIX = 'common.lib.LLDbConnection.';

	public function getConnect()
	{
		return $this->_connect;
	}

	public function setConnect($connect)
	{
		$this->_connect = $connect;
	}

	public function getPdoInstance()
	{
		if ($this->_connect)
		{
			$report_level = error_reporting(0);
			//数据库重新连接
			try
			{
				if ($this->getServerInfo() === 'MySQL server has gone away')
				{
					$this->close();
					parent::open();
					Yii::log("mysql reconnect is success", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
				}
			}
			catch (Exception $e)
			{
				if (strpos($e->getMessage(), 'gone away') !== false)
				{
					Yii::log("Exception：mysql reconnect start [{$e->getCode()}] errMsg[{$e->getMessage()}]", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
					$this->close();
					parent::open();
					Yii::log("Exception：mysql reconnect end [{$e->getCode()}] errMsg[{$e->getMessage()}]", CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
				}
				else
				{
					Yii::log("Exception：errCode[{$e->getCode()}] errMsg[{$e->getMessage()}]", CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
				}
			}
			//error_reporting($report_level);
		}
		$this->_pdo = parent::getPdoInstance();
		return $this->_pdo;
	}

}
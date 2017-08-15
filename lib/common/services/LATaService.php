<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/14/17
 * Time: 22:12
 */
class LATaService
{
    const LOG_PREFIX = 'admin.services.LATaService.';

    public static function getById($ppid)
    {
        return LATaModel::model()->findByPk($ppid);
    }

    public static function getTaByPPid($ppid)
    {
        $criteria = new CDbCriteria();

        $criteria->compare('ppid', $ppid, false);

        return LATaModel::model()->find($criteria);
    }

    public static function create($ppid, $data)
    {
        if(empty($ppid))
        {
            Yii::log("ppid empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        $data['create_time'] = $data['update_time'] = date('Y-m-d H:i:s', time());
        $data['ppid'] = $ppid;

        $objTa = new LATaModel();
        $objTa->setAttributes($data, false);
        if ($objTa->save())
        {
            Yii::log(sprintf("Create ta success, ID[%s]", $objTa->ppid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objTa;
        }
        else
        {
            Yii::log(sprintf("Create ta Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function update($tid, $data)
    {
        if(empty($tid) )
        {
            Yii::log("ppid data empty",CLogger::LEVEL_ERROR,self::LOG_PREFIX.__FUNCTION__);
            return false;
        }

        $objTa = self::getByID($tid);

        if(!$objTa)
        {
            Yii::log(sprintf("Update ta Fail, Get Empty ta info, id:[%s]", $tid),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $data['update_time'] = date('Y-m-d H:i:s', time());

        $objTa->setAttributes($data, false);
        if ($objTa->save())
        {
            Yii::log(sprintf("Update ta success, id[%s]", $objTa->tid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objTa;
        }
        else
        {
            Yii::log(sprintf("Update ta Fail, ID:[%s] Params:[%s]", array($tid, serialize($data))), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function getTaCountByPPid($ppid)
    {
        $criteria = new CDbCriteria();

        $criteria->compare('ppid', $ppid, false);

        return LATaModel::model()->count($criteria);
    }

    public static function deleteTaRecordByPPid($ppid)
    {
        return LATaModel::model()->deleteAll("ppid =:ppid", array(':ppid' => $ppid));
    }
}
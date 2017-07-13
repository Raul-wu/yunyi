<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/11/17
 * Time: 21:21
 */
class LAPProductDetailService
{
    const LOG_PREFIX = 'admin.services.LAPProductDetailService.';

    public static function getByID($ppid)
    {
        return LAPProductDetailModel::model()->findByPk($ppid);
    }

    public static function create($ppid, $data)
    {
        if(empty($ppid) || empty($data))
        {
            Yii::log(sprintf("Create pproduct detail Fail, Create Params Empty"), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $data['ppid'] = $ppid;
        $data['create_time'] = $data['update_time'] = time();

        $objPProductDetail = new LAPProductDetailModel();
        $objPProductDetail->setAttributes($data, false);
        if ($objPProductDetail->save())
        {
            Yii::log(sprintf("Create pproduct detail success, ID[%s]", $objPProductDetail->pdid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objPProductDetail;
        }
        else
        {
            Yii::log(sprintf("Create pproduct detail Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function update($ppid, $data)
    {
        if(empty($ppid))
        {
            Yii::log(sprintf("Update pproduct detail Fail, Update id Empty"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $objPProductDetail = self::getByID($ppid);

        if(!$objPProductDetail)
        {
            Yii::log(sprintf("Update pproduct Fail, Get Empty accountInfo, id:[%s]", $ppid),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $data['update_time'] = time();

        $objPProductDetail->setAttributes($data, false);
        if ($objPProductDetail->save())
        {
            Yii::log(sprintf("Update pproduct detail success, id[%s]", $objPProductDetail->ppid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objPProductDetail;
        }
        else
        {
            Yii::log(sprintf("Update pproduct detail Fail, ID:[%s] Params:[%s]", array($ppid, serialize($data))), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }
}
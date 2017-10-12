<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 10/12/17
 * Time: 22:13
 */
class LACooperateService
{
    const LOG_PREFIX = 'admin.services.LACooperateService.';

    public static function getByID($cid)
    {
        return LACooperateModel::model()->findByPk($cid);
    }

    public static function getAll($arrCondition = array(), $page = 1, $perPage = 10, $order = '')
    {
        $criteria = new CDbCriteria();
        $strUrl = '?';

        if(isset($arrCondition['name']) && !empty($arrCondition['name']))
        {
            $criteria->compare('name', $arrCondition['name'], true);
            $strUrl .= "&name={$arrCondition['name']}";
        }

        $criteria->order = $order ? $order : 'cid desc ';
        $count = LACooperateModel::model()->count($criteria);

        $pageBar = "";
        if($page > 0)
        {
            $criteria->limit  = $perPage;
            $criteria->offset = ($perPage * ($page - 1));
            $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);
        }

        $objAccount = LACooperateModel::model()->findAll($criteria);

        $arrAccount = array_map(function($list){
            return $list->attributes;
        }, $objAccount);

        return array('cooperateAll' => $arrAccount,'pageBar' => $pageBar,'count' => $count);
    }

    public static function Create($data)
    {
        if(empty($data))
        {
            Yii::log(sprintf("Create account Fail, Create Params Empty"), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        if(!isset($data['create_time']))
        {
            $data['create_time'] = date('Y-m-d H:i:s', time());
        }

        if(!isset($data['update_time']))
        {
            $data['update_time'] = date('Y-m-d H:i:s', time());
        }

        $objCooperate = new LACooperateModel();
        $objCooperate->setAttributes($data, false);
        if ($objCooperate->save())
        {
            Yii::log(sprintf("Create Cooperate success, ID[%s]", $objCooperate->cid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objCooperate;
        }
        else
        {
            Yii::log(sprintf("Create Cooperate Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function Update($id, $data)
    {
        if(empty($id))
        {
            Yii::log(sprintf("Update Cooperate Fail, Update id Empty"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $objCooperate = self::getByID($id);

        if(!$objCooperate)
        {
            Yii::log(sprintf("Update Cooperate Fail, Get Empty CooperateInfo, id:[%s]", $id),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        if(!isset($data['update_time']))
        {
            $data['update_time'] = date('Y-m-d H:i:s', time());
        }

        $objCooperate->setAttributes($data, false);
        if ($objCooperate->save())
        {
            Yii::log(sprintf("Update Cooperate success, id[%s]", $objCooperate->cid),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objCooperate;
        }
        else
        {
            Yii::log(sprintf("Update Cooperate Fail, ID:[%s] Params:[%s]", array($id, serialize($data))), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }
}
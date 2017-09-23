<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/10/17
 * Time: 22:10
 */
class LAAccountService
{
    const LOG_PREFIX = 'admin.services.LAAccountService.';

    public static function getAll($arrCondition = array(), $page = 1, $perPage = 10, $order = '')
    {
        $criteria = new CDbCriteria();
        $strUrl = '?';

        if(isset($arrCondition['name']) && !empty($arrCondition['name']))
        {
            $criteria->compare('name', $arrCondition['name'], true);
            $strUrl .= "&name={$arrCondition['name']}";
        }

        if(isset($arrCondition['fund_code']) && !empty($arrCondition['fund_code']))
        {
            $criteria->compare('fund_code', $arrCondition['fund_code'], true);
            $strUrl .= "&fund_code={$arrCondition['fund_code']}";
        }

        $criteria->order = $order ? $order : 'id desc ';
        $count = LAAccountModel::model()->count($criteria);

        $pageBar = "";
        if($page > 0)
        {
            $criteria->limit  = $perPage;
            $criteria->offset = ($perPage * ($page - 1));
            $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);
        }

        $objAccount = LAAccountModel::model()->findAll($criteria);

        $arrAccount = array_map(function($list){
            return $list->attributes;
        }, $objAccount);

        return array('accountAll' => $arrAccount,'pageBar' => $pageBar,'count' => $count);
    }

    public static function getByID($id)
    {
        return LAAccountModel::model()->findByPk($id);
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

        $objAccount = new LAAccountModel();
        $objAccount->setAttributes($data, false);
        if ($objAccount->save())
        {
            Yii::log(sprintf("Create Account success, ID[%s]", $objAccount->id),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objAccount;
        }
        else
        {
            Yii::log(sprintf("Create Account Fail, Params:[%s]",serialize($data)), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function Update($id, $data)
    {
        if(empty($id))
        {
            Yii::log(sprintf("Update account Fail, Update id Empty"),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        $objAccount = self::getByID($id);

        if(!$objAccount)
        {
            Yii::log(sprintf("Update account Fail, Get Empty accountInfo, id:[%s]", $id),CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }

        if(!isset($data['update_time']))
        {
            $data['update_time'] = date('Y-m-d H:i:s', time());
        }

        $objAccount->setAttributes($data, false);
        if ($objAccount->save())
        {
            Yii::log(sprintf("Update account success, id[%s]", $objAccount->id),CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
            return  $objAccount;
        }
        else
        {
            Yii::log(sprintf("Update account Fail, ID:[%s] Params:[%s]", array($id, serialize($data))), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return  false;
        }
    }

    public static function getActiveByPPid($ppid)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('ppid', $ppid);
        $criteria->compare('status', LAAccountModel::STATUS_OPEN);
        $criteria->select = "handler";
        return LAAccountModel::model()->find($criteria);
    }
}
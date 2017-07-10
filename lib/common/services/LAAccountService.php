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

        $criteria->order = $order ? $order : 'id desc ';
        $count = LAAccountModel::model()->count($criteria);

        $criteria->limit  = $perPage;
        $criteria->offset = ($perPage * ($page - 1));
        $pageBar = LAdminPager::getPages($count, $page, $perPage, $strUrl);

        $objAccount = LAAccountModel::model()->findAll($criteria);

        $arrAccount = array_map(function($list){
            return $list->attributes;
        }, $objAccount);

        return array('accountAll' => $arrAccount,'pageBar' => $pageBar,'count' => $count);
    }

    public static function add()
    {

    }
}
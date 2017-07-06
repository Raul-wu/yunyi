<?php

/**
 * Created by PhpStorm.
 * User: mmao
 * Date: 2017/6/14
 * Time: 10:12
 */
class LANewsService
{
    const LOG_PREFIX = 'admin.services.LANewsService.';
    /**
     * 查询资讯信息列表
     * @param array $params
     * @param int $page
     * @param int $limit
     * @param string $order
     * @return array
     */
    public static function getAll($params = array(), $page = 1, $limit = 10, $order = "id DESC"){
        $criteria = new CDbCriteria();
        foreach ($params as $field => $val)
        {
            $criteria->compare($field, $val);
        }
        $criteria->offset = $limit * ($page - 1);
        $criteria->limit = $limit;
        $criteria->order = $order;

        //总条数
        $count = LANewsModel::model()->count($criteria);

        //数据
        $lists = LANewsModel::model()->findAll($criteria);
        $listArr = array_map(function($list){
            return $list->attributes;
        }, $lists);

        return array(
            "list" => $listArr,
            "count" => $count
        );
    }

    /**
     * 新增资讯
     */
    public static function saveNews($params){
        $news = new LANewsModel();
        $news->setAttributes($params, false);
        if ($news->save()) {
            Yii::log(sprintf("create news_information success. id[%s]", $news->getIsNewRecord()), CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
        } else {
            Yii::log(sprintf("create news_information failed."), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
        return true;
    }

    /**
     * 根据ID查询资讯信息
     * @param $id
     * @return CActiveRecord
     */
    public static function getNewsById($id){
        return LANewsModel::model()->findByPk($id);
    }

    /**
     * 更新资讯
     */
    public static function updateNews($params){
        $news = LANewsModel::model()->findByPk($params["id"]);
        $news->setAttributes($params, false);
        if ($news->save()) {
            Yii::log(sprintf("update news_information success. id[%s]", $news->getIsNewRecord()), CLogger::LEVEL_TRACE, self::LOG_PREFIX . __FUNCTION__);
        } else {
            Yii::log(sprintf("update news_information failed."), CLogger::LEVEL_ERROR, self::LOG_PREFIX . __FUNCTION__);
            return false;
        }
        return true;
    }
}
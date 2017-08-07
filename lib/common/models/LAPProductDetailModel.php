<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/11/17
 * Time: 21:20
 */
class LAPProductDetailModel extends LActiveRecord
{
    public function tableName()
    {
        return 'pproduct_detail';
    }

    public function getDbConnection()
    {
        return Yii::app()->yuyinDB;
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
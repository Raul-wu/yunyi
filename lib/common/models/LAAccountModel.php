<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/10/17
 * Time: 22:13
 */
class LAAccountModel extends LActiveRecord
{

    public function tableName()
    {
        return 'account';
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
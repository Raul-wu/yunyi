<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/10/17
 * Time: 22:13
 */
class LAAccountModel extends LActiveRecord
{
    const STATUS_OPEN = 1; //启用
    const STATUS_STOP = 2; //停用

    const OP_TYPE_ADD = 'add';  //添加
    const OP_TYPE_MODIFY = 'modify'; //修改


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
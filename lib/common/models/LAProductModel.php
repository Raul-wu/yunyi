<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/18/17
 * Time: 09:10
 */
class LAProductModel extends LActiveRecord
{
    const STATUS_DRAFT = 1; //草稿
    const STATUS_WAITING_VERIFY = 2;  //待审核
    const STATUS_VERIFY = 3;    //已审核
    const STATUS_DURATION = 4;    //存续中
    const STATUS_DELETE = 5;    //删除

    public static $arrStatus = array(
        self::STATUS_DRAFT => '草稿',
        self::STATUS_WAITING_VERIFY => '待审核',
        self::STATUS_VERIFY => '已审核',
        self::STATUS_DURATION => '存续中',
        self::STATUS_DELETE => '删除'
    );

    public function relations()
    {
        return array('pproduct' => array(self::HAS_ONE, 'LAPProductModel', '', 'on' => 't.ppid = pproduct.ppid'),);
    }

    public function tableName()
    {
        return 'product';
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
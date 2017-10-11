<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/18/17
 * Time: 09:10
 */
class LAProductModel extends LActiveRecord
{
    const STATUS_ESTABLISH = 1; //成立
    const STATUS_DURATION = 2;  //存续中
    const STATUS_DELETE = 3;    //删除
    const STATUS_FINISH = 4;   //已结息

    const CATEGORY_NONE = 0;
    const CATEGORY_A1 = 1;
    const CATEGORY_A2 = 2;
    const CATEGORY_A3 = 3;
    const CATEGORY_A4 = 4;
    const CATEGORY_A5 = 5;

    public static $arrStatus = array(
        self::STATUS_ESTABLISH => '成立',
        self::STATUS_DURATION => '存续中',
        self::STATUS_DELETE => '删除',
        self::STATUS_FINISH => '已结息',
    );

    public static $arrCategory = array(
        self::CATEGORY_NONE => '无',
        self::CATEGORY_A1 => 'A1',
        self::CATEGORY_A2 => 'A2',
        self::CATEGORY_A3 => 'A3',
        self::CATEGORY_A4 => 'A4',
        self::CATEGORY_A5 => 'A5',
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
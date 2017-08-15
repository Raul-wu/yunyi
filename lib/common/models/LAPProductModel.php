<?php

/**
 * Created by PhpStorm.
 * User: Raul
 * Date: 7/11/17
 * Time: 20:57
 */
class LAPProductModel extends LActiveRecord
{
    const STATUS_ESTABLISH = 1; //成立
    const STATUS_DURATION = 2;  //存续
    const STATUS_FINISH = 3;    //已结息
    const STATUS_DELETE = 4;    //已废弃
    const STATUS_WAIT = 5; //待清算

    //状态
    public static $arrStatus = array(
        self::STATUS_ESTABLISH => '成立',
        self::STATUS_DURATION => '存续',
        self::STATUS_FINISH => '已结息',
        self::STATUS_DELETE => '已废弃'
    );

    //产品结构
    const STRUCT_PALN = 1;
    const STRUCT_LIMIT = 2;
    const STRUCT_INTRUST = 3;
    const STRUCT_NORMAL = 4;
    public static $arrStruct = array(
        self::STRUCT_PALN => '资管计划',
        self::STRUCT_LIMIT => '有限合伙',
        self::STRUCT_INTRUST => '信托',
        self::STRUCT_NORMAL => '一般固定收益类契约型基金',
    );

    //产品类型
    const TYPE_FI = 1;
    const TYPE_SM = 2;
    public static $arrType = array(
        self::TYPE_FI => '固定收益',
        self::TYPE_SM => '浮动收益'
    );

    //收益分配方式
    const MODE_NATURE = 1;
    const MODE_QUARTER = 2;
    const MODE_TWENTY = 3;
    const MODE_OTHERS = 4;
    public static $arrMode = array(
        self::MODE_NATURE => '自然季度分配',
        self::MODE_QUARTER => '每满三个月的分配收益一次',
        self::MODE_TWENTY => '第一、第三季度末月20号',
        self::MODE_OTHERS => '其它',
    );

    //计息原则
    const INTERSET_PRINCIPLE_360 = 1;
    const INTERSET_PRINCIPLE_365 = 2;
    public static $arrPrinciple = array(
        self::INTERSET_PRINCIPLE_360 => '30/360天',
        self::INTERSET_PRINCIPLE_365 => '31/365天',
    );

    public function tableName()
    {
        return 'pproduct';
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
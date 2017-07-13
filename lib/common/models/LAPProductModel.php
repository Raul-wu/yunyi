<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/11/17
 * Time: 20:57
 */
class LAPProductModel extends LActiveRecord
{
    const STATUS_ESTABLISH = 1; //成立
    const STATUS_DURATION = 2;  //存续
    const STATUS_FINISH = 3;    //已结息
    const STATUS_DELETE = 4;    //已废弃

    const SELECT_YES = 1; //是
    const SELECT_NO = 2;  //否

    //货源属性
    const GOODS_TYPE_EXCHANGE = 1;
    const GOODS_TYPE_NEW = 2;
    public static $arrGoodTypes = array(
        self::GOODS_TYPE_EXCHANGE          => "大V/机构现货转让",
        self::GOODS_TYPE_NEW        => "凑份子买新货",
    );

    //产品结构
    const STRUCT_PALN = 1;
    const STRUCT_LIMIT = 2;
    const STRUCT_INTRUST = 3;
    public static $arrStruct = array(
        self::STRUCT_PALN => '资管计划',
        self::STRUCT_LIMIT => '有限合伙',
        self::STRUCT_INTRUST => '信托'
    );

    //产品类型
    const PROJECT_TYPE_FI = 1;
    const PRODUCT_TYPE_SM = 2;
    public static $arrProjectType = array(
        self::PROJECT_TYPE_FI => '固定收益',
        self::PRODUCT_TYPE_SM => '浮云收益'
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

    public static $arrYesOrNo = array(
        self::SELECT_YES => '是',
        self::SELECT_NO => '否'
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
<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/24/17
 * Time: 20:01
 */
class LAQuotientModel extends LActiveRecord
{
    const STATUS_OPEN = 1; //启用
    const STATUS_DEL = 2; //删除
    const STATUS_DURATION = 3; //存续中
    const STATUS_FINISH = 4; //已结清

    public static $arrStatus = array(
        self::STATUS_OPEN => '启用',
        self::STATUS_DEL => '删除',
        self::STATUS_DURATION => '存续中',
        self::STATUS_FINISH => '已结清',
    );

    const TYPE_SELF = 1; //投资类型 个人
    const TYPE_COMPANY = 2; //机构

    public static $arrType = array(
        self::TYPE_SELF => '个人',
        self::TYPE_COMPANY => '机构',
    );

    public static $arrTypeReversal = array(
        '个人' => self::TYPE_SELF,
        '机构' => self::TYPE_COMPANY
    );

    const ID_TYPE_SELF = 1; //身份证
    const ID_TYPE_COMPANY = 2;//营业执照

    public static $arrIdType = array(
        self::ID_TYPE_SELF => '身份证',
        self::ID_TYPE_COMPANY => '营业执照',
    );

    public static $arrIdTypeReversal = array(
        '身份证' => self::ID_TYPE_SELF,
        '营业执照'=>self::ID_TYPE_COMPANY ,
    );

    public function relations()
    {
        return array(
            'product' => array(self::HAS_ONE, 'LAProductModel', '', 'on' => 't.pid = product.pid'),
            'pproduct' => array(self::HAS_ONE, 'LAPProductModel', '', 'on' => 'product.ppid = pproduct.ppid')
            );
    }

    public function tableName()
    {
        return 'quotient';
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
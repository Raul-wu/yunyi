<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 10/12/17
 * Time: 21:56
 */
class LACooperateModel extends LActiveRecord
{
    const STATUS_OPEN = 1; //启用
    const STATUS_STOP = 2; //停用

    const NATURE_TYPE_TOGTHER = 1;//合伙企业
    const NATURE_TYPE_LIMITATION = 2;//有限责任公司
    const NATURE_TYPE_OTHER = 3;//其他

    public static $arrNature = array(
        self::NATURE_TYPE_TOGTHER => '合伙企业',
        self::NATURE_TYPE_LIMITATION => '有限责任公司',
        self::NATURE_TYPE_OTHER => '其他',
    );

    const TAX_NO = 0; //核税情况 否
    const TAX_YES = 1;//核税情况 是

    public static $arrTax = array(
        self::TAX_NO => '否',
        self::TAX_YES => '是',
    );

    const ACCOUNT_TYPE_BASE = 0; //基本开户行
    const ACCOUNT_TYPE_BASE_ACCOUNT = 1; //基本户账户
    const ACCOUNT_TYPE_GENERAL = 2;//一般户开户行
    const ACCOUNT_TYPE_GENERAL_ACCOUNT = 3;//一般户账号
    const ACCOUNT_TYPE_COLLECT = 4;//募集开户行
    const ACCOUNT_TYPE_COLLECT_ACCOUNT = 5;//募集户账号
    const ACCOUNT_TYPE_TRUSTEESHIP = 6;//托管开户行
    const ACCOUNT_TYPE_TRUSTEESHIP_ACCOUNT = 7;//托管户账号

    public static $arrAccountType = array(
        self::ACCOUNT_TYPE_BASE => '基本开户行',
        self::ACCOUNT_TYPE_BASE_ACCOUNT => '基本户账户',
        self::ACCOUNT_TYPE_GENERAL => '一般户开户行',
        self::ACCOUNT_TYPE_GENERAL_ACCOUNT => '一般户账号',
        self::ACCOUNT_TYPE_COLLECT => '募集开户行',
        self::ACCOUNT_TYPE_COLLECT_ACCOUNT => '募集户账号',
        self::ACCOUNT_TYPE_TRUSTEESHIP => '托管开户行',
        self::ACCOUNT_TYPE_TRUSTEESHIP_ACCOUNT => '托管户账号',
    );

    public function tableName()
    {
        return 'cooperate';
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
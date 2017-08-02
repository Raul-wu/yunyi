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

    public static $arrStatus = array(
        self::STATUS_ESTABLISH => '成立',
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
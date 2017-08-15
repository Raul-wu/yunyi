<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/14/17
 * Time: 22:07
 */
class LATaModel extends LActiveRecord
{
    const TERM_TOTAL = 1; //到期本息
    const TERM_MIDDLE = 2; //多次分配，期间分配
    const TERM_LAST = 3; //多次分配，末次分配

    public static $arrTerm = array(
        self::TERM_TOTAL => '到期本息',
        self::TERM_MIDDLE => '多次分配，期间分配',
        self::TERM_LAST => '多次分配，末次分配',
    );

    public function relations()
    {
        return array(
            'pproduct' => array(self::HAS_ONE, 'LAPProductModel', '', 'on' => 't.ppid = pproduct.ppid')
        );
    }

    public function tableName()
    {
        return 'ta';
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
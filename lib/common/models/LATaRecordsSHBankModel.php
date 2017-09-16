<?php

/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 9/10/17
 * Time: 15:57
 */
class LATaRecordsSHBankModel extends LActiveRecord
{
    public function tableName()
    {
        return 'ta_records_shanghai_bank';
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
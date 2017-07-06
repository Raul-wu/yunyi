<?php
class LALogModel extends LMongoDocument
{

    //这里要自己完善一下
	public  $collectName; //表名称
    public  $operator; //操作人
    public  $operationType; //状态 0插入 1修改
    public  $detail; // array key为修改的字段，value为修改的新旧对比
    public  $createTime;
    public  $updateTime;


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getCollectionName()
	{
		return 'admin_log';
	}




}


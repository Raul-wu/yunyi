<?php
class LANewRoleModel extends LMongoDocument
{

    //这里要自己完善一下
    public  $_id;
	public  $roleName; //角色名称
    public  $roleContent; //角色注释
    public  $roleSort; //排序
    public  $state; //状态 1有效 0无效
    public  $authority; //权限
    public  $creatorId; //添加人ID
    public  $createTime;
    public  $updateTime;
    public  $_intFields = array('roleSort', 'creatorId', 'state');

    const STATE_OPEN = 1;
    const STATE_CLOSED = 0;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getCollectionName()
	{
		return 'role';
	}




}


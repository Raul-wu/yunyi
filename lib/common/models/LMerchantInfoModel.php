<?php
/**
 * This is the model class for table "common.merchantInfo"
 * @property integer $spvId
 * @property integer $name
 * @property integer $showName
 * @property integer $account
 * @property integer $createTime
 * @property integer $updateTime
 * @property integer $address
 * @property integer $stamp
 * @property bool $isBeneAccount
 * @property integer $state
 * @property integer $createUserId
 * @property integer $settlement
 * @property text $remarks
 */

class LMerchantInfoModel extends LActiveRecord
{
	//C++用于打款状态
	const STATE_INITIAL = 0;	//初始化
	const STATE_NORMAL  = 1;	//启用
	const STATE_DELETE  = 2;	//删除

	const SETTLEMENT_AUTO 	= 1;	//自动结算
	const SETTLEMENT_NOTICE = 0;	//通知结算

	//管理后台用于母产品显示状态
	const IS_BENEACCOUNT  = 1;	//启用（收款账户）
	const NOT_BENEACCOUNT = 0;	//停用（非收款账户）

	public static $arrSettlement = array(
		self::SETTLEMENT_AUTO 	=> "自动结算",
		self::SETTLEMENT_NOTICE => "通知结算",
	);

	public static $arrState = array(
		self::STATE_INITIAL => '初始化',
		self::STATE_NORMAL  => '启用',
		self::STATE_DELETE  => '停用',
	);

	public static $arrBeneAccount = array(
		self::IS_BENEACCOUNT  => '启用',
		self::NOT_BENEACCOUNT => '禁用',
	);

	public function getDbConnection()
	{
		return Yii::app()->commonDb;
	}

	public function tableName()
	{
		return 'merchantInfo';
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
} 
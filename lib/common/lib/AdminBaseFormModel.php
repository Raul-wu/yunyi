<?php
/**
 * @author: deliliu liuwenjie@e-neway.com
 * @since: 8/5/14 15:09
 */

class AdminBaseFormModel extends CFormModel
{
	public function beginDate($value)
	{
		return $value ? CDateTimeParser::parse($value, 'yyyy-MM-dd') : 0;
	}

	public function endDate($value)
	{
		return $value ? CDateTimeParser::parse($value . ' 23:59:59', 'yyyy-MM-dd hh:mm:ss') : 0;
	}

	public function beginDateTime($value)
	{
		return $value ? CDateTimeParser::parse($value . ':00', 'yyyy-MM-dd hh:mm:ss') : 0;
	}

	public function endDateTime($value)
	{
		return $value ? (CDateTimeParser::parse($value . ':00', 'yyyy-MM-dd hh:mm:ss') - 1) : 0;
	}

	public function trim($value)
	{
		return str_replace("　", "",  trim($value));
	}

	/**
	 * 母产品名称不能包含 单引号 双引号 斜杠
	 */
	public function productNameChk()
	{
		$data = $this->attributes;
		preg_match_all('/[\\/\'"\\\]/',$data['productName'],$match);
		if($match[0])
		{
			$this->addError('productName','母产品名称不能包含特殊符号！');
		}
	}
} 
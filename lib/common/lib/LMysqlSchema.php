<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 2015/7/10
 * Time: 18:09
 */

class LMysqlSchema extends CMysqlSchema
{
	protected function findColumns($table)
	{
		$sql='SHOW COLUMNS FROM '.$table->rawName;
		try
		{
			$columns=$this->getDbConnection()->createCommand($sql)->queryAll();
		}
		catch(Exception $e)
		{
			return false;
		}
		foreach($columns as $column)
		{
			$c=$this->createColumn($column);
			$table->columns[$c->name]=$c;
			if($c->isPrimaryKey)
			{
				if($table->primaryKey===null)
					$table->primaryKey=$c->name;
				elseif(is_string($table->primaryKey))
					$table->primaryKey=array($table->primaryKey,$c->name);
				else
					$table->primaryKey[]=$c->name;
				if($c->autoIncrement)
					$table->sequenceName='';
			}
		}
		return true;
	}

	/**
	 * Collects the foreign key column details for the given table.
	 * @param CMysqlTableSchema $table the table metadata
	 */
	protected function findConstraints($table)
	{
		//屏蔽掉 show create table
	}
}
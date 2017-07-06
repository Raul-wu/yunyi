<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 2015/7/10
 * Time: 16:38
 */

class LBaseDbConnection extends CDbConnection
{
	public $driverMap=array(
		'pgsql'=>'CPgsqlSchema',    // PostgreSQL
		'mysqli'=>'LMysqlSchema',   // MySQL
		'mysql'=>'LMysqlSchema',    // MySQL
		'sqlite'=>'CSqliteSchema',  // sqlite 3
		'sqlite2'=>'CSqliteSchema', // sqlite 2
		'mssql'=>'CMssqlSchema',    // Mssql driver on windows hosts
		'dblib'=>'CMssqlSchema',    // dblib drivers on linux (and maybe others os) hosts
		'sqlsrv'=>'CMssqlSchema',   // Mssql
		'oci'=>'COciSchema',        // Oracle driver
	);

	public function __construct($dsn='',$username='',$password='')
	{
		parent::__construct($dsn, $username, $password);

		if (php_sapi_name() === 'cli')
		{
			$this->schemaCachingDuration = 0;
		}
		else
		{
			$this->schemaCachingDuration = 300;
		}
	}

}
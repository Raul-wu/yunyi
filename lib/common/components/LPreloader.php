<?php
/**
 * @author: deliliu liuwenjie@noahwm.com
 * @since: 6/16/14 1:25
 */

class LPreloader extends CApplicationComponent
{
	public $thriftDefinition = array();

	/* @var Thrift\ClassLoader\ThriftClassLoader */
	private $thriftLoader;

	public function init()
	{
		parent::init();

		$this->loadService();

		//载入composer autoload
		Yii::import("common.vendor.autoload", true);
	}

	public function getThriftLoader()
	{
		if (!$this->thriftLoader)
		{
			// 初始化thriftClassLoader
			require_once dirname(__FILE__) . '/../../Thrift/ClassLoader/ThriftClassLoader.php';
			$this->thriftLoader = new Thrift\ClassLoader\ThriftClassLoader();
			$this->thriftLoader->registerNamespace('Thrift', dirname(__FILE__) . '/../../');
			$this->thriftLoader->register();
		}

		return $this->thriftLoader;
	}

	protected function loadService()
	{
		$loader = $this->getThriftLoader();

		$GEN_DIR = dirname(__FILE__) . '/../../rpc-thrift/src_gen/gen-php';
		$GEN_DIR_LCS = dirname(__FILE__) . '/../service/lcs-gen-php';
		$loader->registerDefinition('entity', $GEN_DIR);
		$loader->registerDefinition('service', $GEN_DIR);
		$loader->registerDefinition('msgq', $GEN_DIR);
		$loader->registerDefinition('external', $GEN_DIR);
		$loader->registerDefinition('lcsserver', $GEN_DIR_LCS);
		$loader->registerDefinition('page', $GEN_DIR);
	}

} 
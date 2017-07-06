<?php
defined('BASE_DOMAIN') or define('BASE_DOMAIN', '.yuyin.com');
defined('FLAG_ORG_ATTR') or define('FLAG_ORG_ATTR', 1);
defined('YII_DEBUG') or define('YII_DEBUG', true);
$config = dirname(__FILE__) . '/../protected/config/main.php';
// include Yii bootstrap file
require_once(dirname(__FILE__) . '/../../lib/framework/yii.php');
// create a Web application instance and run
Yii::createWebApplication($config)->run();

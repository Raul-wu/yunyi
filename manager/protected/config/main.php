<?php

// remove the following line when in production mode

$adminConfig = include(__DIR__ . '/../../../lib/common/config/idc.php');
$baseConfig = include('base.php');
$webConfig = array(
    'name' => '管理后台',
	'params' => array(
		'gaTrackId' => 'UA-51407249-1',
	),
);

$config = CMap::mergeArray($adminConfig, $baseConfig, $webConfig);

return $config;

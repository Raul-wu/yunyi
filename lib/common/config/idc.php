<?php
$baseConfig = include('base.php');

defined('ENVIRONMENT') or define('ENVIRONMENT', ENVIRONMENT_IDC);

$commonConfig = array(
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'yuyin@1234',
            'ipFilters' => array('10.*'),
            'newFileMode' => 0777,
            // 'newDirMode'=>0777,
        ),
    ),
    'components' => array(
        'cache' => array(
            'class' => 'common.components.LRedisCache',
            'directoryLevel' => 2,
        ),
        'sessionCache' => array(
            'servers' => array(
                array('host' => '10.23.52.56', 'port' => 6379),
            ),
        ),
        'cache' => array(
            'servers' => array(
                array('host' => '10.23.52.56', 'port' => 6379),
            ),
        ),
        'yuyinDB' => array(
            'connectionString' => 'mysql:host=10.23.10.79;dbname=yuyi_ta',
            'username' => 'root',
            'password' => 'SbeSFKUXJ27sd9u',
        ),
        'mongodb' => array(
            'class' => 'LMongoDB',
            'connectionString' => 'mongodb://root:t9pCFSb7JrEeQMSk@10.23.54.112/admin',
            'dbName' => 'yuyin_admin',
            'fsyncFlag' => true,
            'safeFlag' => true,
            'useCursor' => false
        ),



    ),
    'params' => array(),
);

return CMap::mergeArray($baseConfig, $commonConfig);

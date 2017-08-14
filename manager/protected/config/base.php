<?php

defined('STATIC_VER') or define('STATIC_VER', time());
defined('BASE_DOMAIN') or define('BASE_DOMAIN', '.yuyin.com');

return array(
	'basePath' => '../protected',
	'defaultController' => 'index',
    'import' => array(
        'application.components.*',
    ),

	'components' => array(
		'clientScript' => array(
			//'coreScriptUrl' => YII_DEBUG ? '/assets/src/js' : '/assets/dist/js',
			'coreScriptUrl' => '/assets/src/js',
			'coreScriptPosition' => CClientScript::POS_END,
			'defaultScriptFilePosition' => CClientScript::POS_END,
			'packages' => array(
				'requirejs' => array(
					'js' => array('require.js', 'config.js?v=' . STATIC_VER),
				),
			),
		),
        'errorHandler' => array(
            //'class' => 'admin.lib.AdminErrorHandler',
            'errorAction' => '/user/error',
        ),
        'request' => array(
            'class' => 'common.components.LHttpRequest',

            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
            'noCsrfValidationRoutes' => array(),
        ),
        'session' => array(
            'timeout' => 1200,
        ),
        'log' => array(
            'routes' => array(
                "netLog" => array(
                    'class' => 'common.components.AdminNetLogRoute',
                ),
            ),
        ),
	),
    'params' => array(
        'noCheck' => array(),
    ),
);
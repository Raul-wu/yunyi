<?php
//环境
defined('ENVIRONMENT_DEV') or define('ENVIRONMENT_DEV', 1);
defined('ENVIRONMENT_TEST') or define('ENVIRONMENT_TEST', 2);
defined('ENVIRONMENT_PRE') or define('ENVIRONMENT_PRE', 3);
defined('ENVIRONMENT_IDC') or define('ENVIRONMENT_IDC', 4);
defined('ENVIRONMENT_SH_IDC') or define('ENVIRONMENT_SH_IDC', 5);

return array(
    'aliases' => array(
        'common' => dirname(__DIR__),
        'extensions' => dirname(dirname(__DIR__)) . '/extensions',
    ),
    'preload' => array(
        'log',
        'preloader',
    ),
    'import' => array(
        'common.components.*',
        'common.lib.*',
        'common.misc.*',
        'common.models.*',
        'common.models.saas.*',
        'common.services.*',
        'common.services.saas.*',
        'common.service.FinanceLease.*',
        'common.lib.tcpdf.LTcPDFBase',
        'extensions.YiiMongoDbSuite.*',
        'extensions.ftp.*',
        'common.web.components.*',
        'common.vendor.*',
        'extensions.vendor.*',
        'extensions.wxPay.*',
        'extensions.alipay.*',
        'extensions.Ocr.*',
    ),
    'components' => array(
        'imgUpToken' => array(
            'class' => 'common.components.LQiniuUpToken',
            'scope' => 'assets',
            'mimeLimit' => 'image/*',
            'returnBody' => '{"url": $(key)}',
            'SecretKey' => 'cioN4nKMfUa1Fo7zwCFlxBy72fk_-iyZMFxSBSqj',
            'AccessKey' => 'Yp8bugf6wRMhl8Fv5LtiHUyzmzB5hYJxfVh1ztLW',
        ),
        'fileUpToken' => array(
            'class' => 'common.components.LQiniuUpToken',
            'scope' => 'assets',
            'fsizeLimit' => 1024 * 1024 * 200, //52M
            'mimeLimit' => 'application/*;application/pdf;image/*',
            'returnBody' => '{"url": $(key)}',
            'SecretKey' => 'cioN4nKMfUa1Fo7zwCFlxBy72fk_-iyZMFxSBSqj',
            'AccessKey' => 'Yp8bugf6wRMhl8Fv5LtiHUyzmzB5hYJxfVh1ztLW',
        ),
        //mail
        'mailNoreply' => array(
            'class' => 'common.components.Mail',
            'host' => "smtp.exmail.com",
            'user' => "no-reply@email.com",
            'password' => "Neway123456",
            'port' => "25",
            'from' => "no-reply@email.com",
            'fromName' => "xxxx",
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'appendParams' => false,
        ),
        'preloader' => array(
            'class' => 'common.components.LPreloader',
        ),
        // 内部组件
        'request' => array(
            'class' => 'common.components.LHttpRequest',
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
            'csrfCookie' => array(
                'httpOnly' => true,
            ),
            'csrfTokenName' => 'g_tk',
        ),
//		'session' => array(
//			'class' => 'CCacheHttpSession',
//			'cookieParams' => array(
//				'domain' => BASE_DOMAIN,
//				'httponly' => true,
//			),
//			'cacheID' => 'sessionCache',
//		),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                "netLog" => array(
                    'class' => 'LNetLogRoute',
                    'levels' => 'error, warning, trace, info, profile',/* @see CLogger::LEVEL_TRACE 参考 */
                ),
            ),
        ),
        'user' => array(
            'class' => 'common.components.LWebUser',
            'stateKeyPrefix' => 'skey',
            'allowAutoLogin' => true,
        ),
        'statePersister' => array(
            'class' => 'common.components.LCacheStatePersister'
        ),

		'sessionCache' => array(
			'class' => 'common.components.LRedisCache',
			'hashKey' => false,
			'keyPrefix' => 'Y201701.',
		),
		'cache' => array(
			'class' => 'common.components.LRedisCache',
			'hashKey' => false,
			'keyPrefix' => 'Y201702.',
		),
        'redis' => array(
            'class' => 'common.components.LRedisCache',
            'keyPrefix' => '',
        ),

        'yuyinDB' => array(
            'class' => 'CDbConnection',
            'charset' => 'utf8',
        ),

        //产品信息mongodb
        'mongodb' => array(
            'class' => 'LMongoDB',
            'fsyncFlag' => true,
            'safeFlag' => true,
            'useCursor' => false
        ),
        'curl' => array(
            'class' => 'common.components.LComponentCurl',
        ),
        'ftp'=>array(
            'class'=>'extensions.ftp.EFtpComponent',
            'ssl'=>false,
            'timeout'=>90,
            'autoConnect'=>true,
        ),
    ),
    'params' => array(
        'captcha' => array(
            'class' => 'CCaptchaAction',
            'minLength' => 4,
            'maxLength' => 4,
            'fontFile' => __DIR__ . '/../lib/font/tribeca.ttf',
        ),
        'pdfAgreeImgDir' => __DIR__ . '/../lib/tcpdf/imgs/',
        'commentMaxLength' => 140,
        'no_check' => array(
            "/user/signin",
            "/user/error",
            "/user/register",
        ),
        'user_operation' => array(
            '/user/signout'
        ),
    ),
);
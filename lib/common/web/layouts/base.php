<?php

/* @var $this AdminBaseController */

Yii::app()->clientScript->registerCssFile("{$this->styleDir}/css/global.css?v=" . STATIC_VER);

if ($this->jsMain) {
	Yii::app()->clientScript->registerCoreScript('requirejs');
	Yii::app()->clientScript->registerScript('require#main', "require(['{$this->jsMain}']);", CClientScript::POS_END);
}

Yii::app()->clientScript->registerScript('global#csrf', 'window.csrf=' . json_encode(array(
		'name' => Yii::app()->request->csrfTokenName,
		'value' => Yii::app()->request->csrfToken,
	)) . ';', CClientScript::POS_HEAD);
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="shortcut icon" href="/assets/src/img/favicon.ico">
	<title><?= $this->pageTitle ?></title>
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>

<body class="<?= $this->bodyClass ?>">

<?= $content ?>

</body>
</html>

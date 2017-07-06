<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-18
 * Time: 下午3:41
 */
$this->setBodyClass('jqui');

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/login.css?v=" . STATIC_VER);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $this->pageTitle ?></title>
</head>

<body>


    <div class="title"></div>
    <h2>Error <?php echo $code; ?></h2>

    <div class="error">
        <?php echo CHtml::encode($message); ?>
    </div>
</body>
</html>

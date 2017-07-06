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
    <link rel="shortcut icon" href="/assets/src/img/favicon.ico">
    <title><?= $this->pageTitle ?></title>
</head>

<body>


<div class="login_mod">
    <div class="title"></div>
    <div class="login_form">
        <form action="<?= Yii::app()->createUrl('user/signin') ?>" method="post">
        <div class="val_group">
            <label class="val_label account">账号</label>
            <div class="val">
                <input type="text"  class="input_text" name="user" value="" >
            </div>
        </div>
        <div class="val_group">
            <label class="val_label pwd">密码</label>
            <div class="val">
                <input type="password" name="password" class="input_text">
            </div>
        </div>
            <span style="color: yellow;"><?= CHtml::encode($err); ?></span>
        <div class="val_action">
            <input type="hidden" name="referrer" value="<?= $referrer ?>">
            <input type="hidden" name="<?= CHtml::encode(Yii::app()->request->csrfTokenName) ?>" value="<?= CHtml::encode(Yii::app()->request->csrfToken) ?>">
            <input type="submit" class="btn btn_login" value="登 录">
        </div>
     </form>
    </div>
</div>
<script>
    var userEl = document.getElementsByName('user')[0];
    var passwordEl = document.getElementsByName('password')[0];

    userEl.value ? passwordEl.focus() : userEl.focus();
</script>
</body>
</html>

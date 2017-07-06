<!doctype html>
<head>
    <meta charset="utf-8">
</head>
<form action="<?= Yii::app()->createUrl('user/signin') ?>" method="post">
	<ul style="list-style-type: none">
		<?php if ($err) { ?><li style="color: #FF0000"><?= CHtml::encode($err) ?></li><?php } ?>
		<li><label>邮箱： <input name="user" placeholder="用户名" value="<?= CHtml::encode($user) ?>" /></label></li>
		<li><label>密码：<input name="password" type="password" placeholder="密码" /></label></li>
		<li><button>登录</button></li>
	</ul>
    <input type="hidden" name="referrer" value="<?= $referrer ?>">
	<input type="hidden" name="<?= CHtml::encode(Yii::app()->request->csrfTokenName) ?>" value="<?= CHtml::encode(Yii::app()->request->csrfToken) ?>">
</form>

<script>
	var userEl = document.getElementsByName('user')[0];
	var passwordEl = document.getElementsByName('password')[0];

	userEl.value ? passwordEl.focus() : userEl.focus();
</script>
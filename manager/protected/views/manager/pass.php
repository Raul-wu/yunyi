<?php
/**
 * Created by PhpStorm.
 * User: john<jiwangli>
 * Date: 14-9-19
 * Time: 下午1:20
 */

$this->setBodyClass('jqui');

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-timepicker.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerScriptFile("/assets/src/js/lib/ckeditor/ckeditor.js", CClientScript::POS_BEGIN);

?>

<div id="main">
    <div class="content">

        <form class="pure-form" action="<?= Yii::app()->createUrl("manager/UpdatePass")?>"  method="post" id="postForm">
            <div class="editor_box">
                <h1>密码修改</h1>
                <div class="editor_form">
                    <div class="pure-g">
                        <div class="pure-u-1"><label>旧密码</label> <input format="number_scientific" id="password" name="password" value="" type="password" class="pure-input-1-3"> *</div>
                        <div class="pure-u-1"><label>新密码</label> <input format="number_scientific" id="password1" name="password1" value="" type="password" class="pure-input-1-3"> *</div>
                        <div class="pure-u-1"><label>确认密码</label> <input format="number_scientific" id="password2" name="password2" value="" type="password" class="pure-input-1-3"> *</div>

                    </div>

                </div>
            </div>

            <div class="form_action">
                <input type="hidden" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"/>
                <button type="submit" class="pure-button pure-button-primary" id="save">提交保存</button>
                <a href="<?= Yii::app()->createUrl('manager/index') ?>" class="pure-button pure-button-primary">返回列表</a>
            </div>

        </form>

    </div>
</div>
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

        <form class="pure-form" action="<?= Yii::app()->createUrl("manager/test")?>"  method="post" id="postForm">
            <div class="editor_box">
                <div class="editor_form">
                    <div class="pure-g">
                        <div class="pure-u-1"><label>内容</label><textarea richtext="ckeditor" placeholder="可不填写,多个请用英文,分隔" name="word"></textarea> </div>
                    </div>

                </div>
            </div>

            <div class="form_action">
                <input type="hidden" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"/>
                <button type="submit" class="pure-button pure-button-primary">提交保存</button>
                <a href="<?= Yii::app()->createUrl('manager/index') ?>" class="pure-button pure-button-primary">返回列表</a>
            </div>

        </form>

    </div>
</div>
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

        <form class="pure-form" action="<?= Yii::app()->createUrl($postUrl)?>"  method="post" id="postForm">
            <div class="editor_box">
                <h1><?= $menu["name"] ?> 菜单权限</h1>
                <div class="editor_form">
                    <div class="pure-g">
                        <div class="pure-u-1"><label>权限ID</label> <input format="number_scientific" id="menuPerId" name="menuPerId" value="<?= $menuPerId ?>" type="text" class="pure-input-1-3"> </div>
                        <div class="pure-u-1"><label>权限URL</label> <input format="number_scientific" id="route" name="route" type="text" class="pure-input-1-3"> </div>
                        <div class="pure-u-1"><label>权限名称</label> <input format="number_scientific" id="name" name="name" value="" type="text" class="pure-input-1-3"> </div>
                    </div>

                </div>
            </div>

            <div class="form_action">
                <input type="hidden" name="menuId" value="<?= !empty($menu["_id"]) ? $menu["_id"] : 0 ?>"/>
                <input type="hidden" name="_id" value="<?= !empty($menuPermission["_id"]) ? $menuPermission["_id"] : 0 ?>"/>
                <input type="hidden" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>"/>
                <button type="submit" class="pure-button pure-button-primary" id="save">提交保存</button>
                <a href="<?= Yii::app()->createUrl('menu/index') ?>" class="pure-button pure-button-primary">返回菜单列表</a>
            </div>

        </form>

    </div>
</div>
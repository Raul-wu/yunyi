<?php
/**
 * Created by PhpStorm.
 * User: Raul
 * Date: 7/18/17
 * Time: 09:09
 */
$this->setBodyClass('jqui');

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-timepicker.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/combo.select.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tip-twitter.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);
?>

<div id="main">
    <div class="content">

        <form class="pure-form" enctype="multipart/form-data" saveAction="<?= Yii::app()->createUrl('quotient/save')?>" method="post" id="form">

        <div class="editor_box">
            <div class="editor_form">
                <h2>导入客户份额表</h2>
                <div class="pure-g">
                    <input  name="pid" type="hidden" value="<?= isset($pid) ?$pid : '' ?>">
                    <input type="hidden" key=''token" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" csrfInput/>

                    <div class="pure-u-1"><label>产品ID</label>
                        <input type="text" class="pure-input-1-2  submit" disabled value="<?= isset($product->pid) ? $product->pid : ''?>">
                    </div>

                    <div class="pure-u-1"><label>产品名称</label>
                        <input type="text" class="pure-input-1-2  submit" disabled value="<?= isset($product->name) ? $product->name : ''?>">
                    </div>

                    <div class="pure-u-1"><label>份额表</label>
                        <input id="quotients" type="file" name="quotients" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                        <span>文件大小不能超过5M</span>
                        <a href="/assets/tml/client_quotient.xlsx" target="_blank" >
                            样例模板下载
                        </a>
                    </div>

                </div>
            </div>
        </div>


        <div class="form_action pure-form">
            <button type="submit" class="pure-button pure-button-primary" id="save">提交保存</button>
        </div>
        </form>

    </div>
</div>
<div id="confirm_div_warp" style="display:none" >
</div>
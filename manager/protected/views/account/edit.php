<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/10/17
 * Time: 22:40
 */
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.structure.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-1.11.0.custom/jquery-ui.theme.min.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/jquery-ui-timepicker.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);
Yii::app()->clientScript->registerScript("backUrl", 'window.backUrl="/account/list";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("saveUrl", 'window.saveUrl="/account/save";', CClientScript::POS_END);
?>
<div id="main">
    <div class="content">

        <form id="spvForm" class="pure-form" action="<?= Yii::app()->createUrl("account/save") ?>" method="post"">
        <input type="hidden" name="id" id="id" value="<?= isset($id) ? $id : ''?>">
        <input type="hidden" name="opType" id="opType" value="<?= $opType ?>">
        <input type="hidden" id="tkName" tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>">

        <div class="editor_box">
            <h1>添加资金账户</h1>
            <div class="editor_form">
                <div class="pure-g">
                    <div class="pure-u-1"><label>账户性质</label>
                        <input type="text" class="pure-input-2-3 submit" name="type" id="type" placeholder="募集户" maxlength="255" value="<?= isset($type) ? $type : ''; ?>" />
                    </div>

                    <div class="pure-u-1"><label>户名</label>
                        <input type="text" class="pure-input-2-3 submit" name="name" id="name" placeholder="户名" maxlength="255" value="<?= isset($name) ? $name : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>银行账号</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_account" id="bank_account" placeholder="" maxlength="255" value="<?= isset($bank_account) ? $bank_account : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>开户行</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_address" id="bank_address" placeholder="" maxlength="255" value="<?= isset($bank_address) ? $bank_address : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>经办人</label>
                        <input type="text" class="pure-input-2-3 submit" name="handler" id="handler" placeholder="" maxlength="255" value="<?= isset($handler) ? $handler : ''; ?>">
                    </div>


                    <div class="pure-u-1"><label>状态</label>
                        <input type="radio" name="status" value="<?= LMerchantInfoModel::IS_BENEACCOUNT ?>" <?= isset($chk_state) ? $chk_state : ''; ?> <?= isset($status) && ($status == LMerchantInfoModel::IS_BENEACCOUNT) ? "checked" : "" ?>/>启用
                        <input type="radio" name="status" value="<?= LMerchantInfoModel::NOT_BENEACCOUNT ?>" <?= isset($isBeneAccount) && ($status == LMerchantInfoModel::NOT_BENEACCOUNT)  ? "checked" : "" ?>/>禁用
                    </div>

                </div>
            </div>
        </div>

        <div class="form_action">
            <?php
            if($opType == 'add')
            {
                if(LAPermissionService::selectMenuPermission($this->menuId, 2006102))
                {
                    ?>
                    <button type="button" class="pure-button pure-button-primary" id="add">保存</button>
                    <?php
                }
            }
            else
            {
                if (LAPermissionService::selectMenuPermission($this->menuId, 2006103))
                {
                    ?>
                    <button type="button" class="pure-button pure-button-primary" id="edit">修改</button>
                    <?php
                }
            }
            ?>
            <button type="button" class="pure-button pure-button-primary" id="back">返回</button>
        </div>
        </form>
    </div>
</div>

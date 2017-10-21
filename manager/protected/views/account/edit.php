<?php
/**
 * Created by PhpStorm.
 * User: Raul
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
        <input type="hidden" name="ppid" id="ppid" value="<?= $ppid ?>">
        <input type="hidden" id="tkName" tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>">

        <div class="editor_box">
            <h1>添加资金账户</h1>
            <div class="editor_form">
                <div class="pure-g">
                    <div class="pure-u-1"><label>基金代码</label>
                        <input type="text" class="pure-input-2-3 submit" name="fund_code" id="fund_code" disabled maxlength="255" value="<?= isset($fund_code) ? $fund_code : ''; ?>" /> *
                    </div>

                    <div class="pure-u-1"><label>账户性质</label>
                        <select class="pure-input-2-3 submit" name="type" id="type" id="type" >
                            <option value="基本户" <?= isset($type) && $type == "基本户" ? "selected=\"selected\"" : "" ?> >基本户</option>
                            <option value="一般户" <?= isset($type) && $type == "一般户" ? "selected=\"selected\"" : "" ?>>一般户</option>
                            <option value="募集户" <?= isset($type) && $type == "募集户" ? "selected=\"selected\"" : "" ?>>募集户</option>
                            <option value="托管户" <?= isset($type) && $type == "托管户" ? "selected=\"selected\"" : "" ?>>托管户</option>
                        </select>
                         *
                    </div>

                    <div class="pure-u-1"><label>户名</label>
                        <input type="text" class="pure-input-2-3 submit" name="name" id="name" placeholder="必填" maxlength="255" value="<?= isset($name) ? $name : ''; ?>"> *
                    </div>

                    <div class="pure-u-1"><label>银行账号</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_account" id="bank_account" placeholder="必填" maxlength="255" value="<?= isset($bank_account) ? $bank_account : ''; ?>"> *
                    </div>

                    <div class="pure-u-1"><label>开户行</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_address" id="bank_address" placeholder="必填" maxlength="255" value="<?= isset($bank_address) ? $bank_address : ''; ?>"> *
                    </div>

                    <div class="pure-u-1"><label>经办人</label>
                        <input type="text" class="pure-input-2-3 submit" name="handler" id="handler" placeholder="必填" maxlength="255" value="<?= isset($handler) ? $handler : ''; ?>"> *
                    </div>


                    <div class="pure-u-1"><label>状态</label>
                        <input type="radio" name="status" value="<?= LAAccountModel::STATUS_OPEN ?>" <?= isset($chk_state) ? $chk_state : ''; ?> <?= isset($status) && ($status == LAAccountModel::STATUS_OPEN) ? "checked" : "" ?>/>正常
                        <input type="radio" name="status" value="<?= LAAccountModel::STATUS_STOP ?>" <?= isset($status) && ($status == LAAccountModel::STATUS_STOP)  ? "checked" : "" ?>/>停用
                    </div>

                </div>
            </div>
        </div>

        <div class="form_action">
            <?php
            if($opType == LAAccountModel::OP_TYPE_ADD)
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

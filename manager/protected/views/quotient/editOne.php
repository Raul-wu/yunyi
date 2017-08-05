<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/5/17
 * Time: 11:54
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

        <form class="pure-form" saveAction="<?= Yii::app()->createUrl('quotient/saveOne')?>" method="post" id="form">
        <input type="hidden" name="pid" id="pid" value="<?= isset($pid) ? $pid : $quotient->pid?>">
        <input type="hidden" name="qid" id="qid" value="<?= isset($qid) ? $qid : ''?>">
        <input type="hidden" key=''token" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" csrfInput/>

        <div class="editor_box">
            <h1>添加客户份额</h1>
            <div class="editor_form">
                <div class="pure-g">
                    <div class="pure-u-1"><label>投资人姓名</label>
                        <input type="text" class="pure-input-2-3 submit" name="name" id="name"  placeholder="必填" value="<?= isset($quotient->name) ? $quotient->name : ''; ?>" /> *
                    </div>

                    <div class="pure-u-1"><label>交易金额（元）</label>
                        <input type="text" class="pure-input-2-3 submit" name="amount" id="amount" placeholder="必填" value="<?= isset($quotient->amount) ? $quotient->amount : ''; ?>" /> *
                    </div>

                    <div class="pure-u-1"><label>投资人类型</label>
                        <select class="pure-input-1-2 "  name="type" >
                            <?php
                            foreach (LAQuotientModel::$arrType as $key => $type)
                            {
                                ?>
                                <option value="<?= $key ?>" <?= isset($quotient->type) && $quotient->type == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($type) ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="pure-u-1"><label>证件类别</label>
                        <select class="pure-input-1-2 " name="id_type" >
                            <?php
                            foreach (LAQuotientModel::$arrIdType as $key => $idType)
                            {
                                ?>
                                <option value="<?= $key ?>" <?= isset($quotient->id_type) && $quotient->id_type == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($idType) ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="pure-u-1"><label>证件号码</label>
                        <input type="text" class="pure-input-2-3 submit" name="id_content" id="id_content"  value="<?= isset($quotient->id_content) ? $quotient->id_content : ''; ?>"> *
                    </div>

                    <div class="pure-u-1"><label>经办人姓名</label>
                        <input type="text" class="pure-input-2-3 submit" name="handler_name" id="handler_name" value="<?= isset($quotient->handler_name) ? $quotient->handler_name : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>法人代表姓名/投资人姓名</label>
                        <input type="text" class="pure-input-2-3 submit" name="delegate_name" id="delegate_name" value="<?= isset($quotient->delegate_name) ? $quotient->delegate_name : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>银行账号</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_account" id="bank_account" value="<?= isset($quotient->bank_account) ? $quotient->bank_account : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>银行户名</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_name" id="bank_name" value="<?= isset($quotient->bank_name) ? $quotient->bank_name : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>开户行名称</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_address" id="bank_address" value="<?= isset($quotient->bank_address) ? $quotient->bank_address : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>开户行省份</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_province" id="bank_province" value="<?= isset($quotient->bank_province) ? $quotient->bank_province : ''; ?>">
                    </div>

                    <div class="pure-u-1"><label>开户行城市</label>
                        <input type="text" class="pure-input-2-3 submit" name="bank_city" id="bank_city" value="<?= isset($quotient->bank_city) ? $quotient->bank_city : ''; ?>">
                    </div>

                </div>
            </div>
        </div>

            <div class="form_action pure-form">
                <button type="submit" class="pure-button pure-button-primary" id="saveOne">提交保存</button>
            </div>
        </form>
    </div>
</div>
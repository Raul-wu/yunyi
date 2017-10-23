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

        <form class="pure-form" saveAction="<?= Yii::app()->createUrl('quotient/saveChange')?>" method="post" id="form">
            <input type="hidden" name="pid" id="pid" value="<?= isset($pid) ? $pid : $quotient->pid?>">
            <input type="hidden" name="qid" id="qid" value="<?= isset($qid) ? $qid : ''?>">
            <input type="hidden" name="amount" id="amount" value="<?= isset($quotient->amount) ? $quotient->amount / LConstService::E4 : ''; ?>">
            <input type="hidden" key=''token" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" csrfInput/>

            <div class="editor_box">
                <h1>客户份额信息</h1>
                <div class="editor_form">
                    <div class="pure-g">
                        <div class="pure-u-md-1-2"><label>投资人姓名:</label>
                            <?= isset($quotient->name) ? $quotient->name : ''; ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>交易金额:</label>
                            <?= isset($quotient->amount) ? $quotient->amount / LConstService::E4 : ''; ?>（万元）
                        </div>

                        <div class="pure-u-md-1-2"><label>投资人类型:</label>
                            <?= isset(LAQuotientModel::$arrType[$quotient->type]) ? LAQuotientModel::$arrType[$quotient->type] : '' ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>证件类别:</label>
                            <?= isset(LAQuotientModel::$arrIdType[$quotient->id_type]) ? LAQuotientModel::$arrIdType[$quotient->id_type] : '' ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>证件号码:</label>
                            <?= isset($quotient->id_content) ? $quotient->id_content : ''; ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>经办人姓名:</label>
                            <?= isset($quotient->handler_name) ? $quotient->handler_name : ''; ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>法人代表姓名/投资人姓名:</label>
                            <?= isset($quotient->delegate_name) ? $quotient->delegate_name : ''; ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>银行账号:</label>
                            <?= isset($quotient->bank_account) ? $quotient->bank_account : ''; ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>银行户名:</label>
                            <?= isset($quotient->bank_name) ? $quotient->bank_name : ''; ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>开户行名称:</label>
                            <?= isset($quotient->bank_address) ? $quotient->bank_address : ''; ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>开户行省份:</label>
                            <?= isset($quotient->bank_province) ? $quotient->bank_province : ''; ?>
                        </div>

                        <div class="pure-u-md-1-2"><label>开户行城市:</label>
                            <?= isset($quotient->bank_city) ? $quotient->bank_city : ''; ?>
                        </div>

                    </div>
                </div>

<?php
if(!empty($quotientChange))
{
?>
                <h1>变更客户份额</h1>
                <table class="pure-table" style="width: 100%">
                    <colgroup>
                        <col class="w_20" />
                        <col class="w_100" />
                        <col class="w_100" />
                        <col class="w_100" />
                        <col class="w_120" />
                        <col class="w_120" />
                        <col class="w_120" />
                        <col class="w_120" />
                        <col class="w_80" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>产品名称</th>
                        <th>投资人姓名</th>
                        <th>交易金额（万元）</th>
                        <th>投资类型</th>
                        <th>证件类别</th>
                        <th>证件号码</th>
                        <th>经办人姓名</th>
                        <th>变更时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                        foreach ($quotientChange as $key => $val)
                        {
                            ?>
                            <tr class="<?= $key % 2 ? "" : "pure-table-odd" ?> pure-table-tr">
                                <td><?= $val['qid'] ?></td>
                                <td class="tc"><?= $val['product']['name'] ?></td>
                                <td class="tc"><?= $val['name'] ?></td>
                                <td class="tc"><?= $val['amount'] / LConstService::E4 ?></td>
                                <td class="tc"><?= isset(LAQuotientModel::$arrType[$val['type']]) ? LAQuotientModel::$arrType[$val['type']] : '' ?></td>
                                <td class="tc"><?= isset(LAQuotientModel::$arrIdType[$val['id_type']]) ? LAQuotientModel::$arrIdType[$val['id_type']] : '' ?></td>
                                <td class="tc"><?= $val['id_content'] ?></td>
                                <td class="tc"><?= $val['handler_name'] ?></td>
                                <td class="tc"><?= $val['update_time'] ?></td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                </table>
                <?php
                }
                ?>
            </div>
        </form>
    </div>
</div>
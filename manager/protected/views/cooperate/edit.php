<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 10/12/17
 * Time: 22:45
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

        <form class="pure-form" saveAction="<?= Yii::app()->createUrl('cooperate/save')?>" method="post" id="form">
            <input type="hidden" name="cid" id="cid" value="<?= isset($cid) ? $cid : ''?>">
            <input type="hidden" key=''token" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" csrfInput/>

            <div class="editor_box">
                <h1>合伙企业/公司信息</h1>
                <div class="editor_form">
                    <div class="pure-g">
                        <div class="pure-u-md-1-2"><label>名称</label>
                            <input type="text" class="pure-input-2-3 submit" name="name" id="name"  placeholder="必填" value="<?= isset($cooperate->name) ? $cooperate->name : ''; ?>" /> *
                        </div>

                        <div class="pure-u-md-1-2"><label>企业性质</label>
                            <select class="pure-input-1-2 "  name="nature" >
                                <?php
                                foreach (LACooperateModel::$arrNature as $key => $nature)
                                {
                                    ?>
                                    <option value="<?= $key ?>" <?= isset($cooperate->nature) && $cooperate->nature == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($nature) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="pure-u-md-1-2"><label>注册地</label>
                            <input type="text" class="pure-input-2-3 submit" name="location" id="location" value="<?= isset($cooperate->location) ? $cooperate->location : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>执行合伙人</label>
                            <input type="text" class="pure-input-2-3 submit" name="cooperater" id="cooperater" value="<?= isset($cooperate->cooperater) ? $cooperate->cooperater : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>有限合伙人</label>
                            <input type="text" class="pure-input-2-3 submit" name="limitation_cooperater" id="limitation_cooperater" value="<?= isset($cooperate->limitation_cooperater) ? $cooperate->limitation_cooperater : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>委派代表</label>
                            <input type="text" class="pure-input-2-3 submit" name="delegate" id="delegate" value="<?= isset($cooperate->delegate) ? $cooperate->delegate : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>项目经理</label>
                            <input type="text" class="pure-input-2-3 submit" name="project_manager" id="project_manager" value="<?= isset($cooperate->project_manager) ? $cooperate->project_manager : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>部门</label>
                            <input type="text" class="pure-input-2-3 submit" name="department" id="department" value="<?= isset($cooperate->department) ? $cooperate->department : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>团队负责人</label>
                            <input type="text" class="pure-input-2-3 submit" name="team_leader" id="team_leader" value="<?= isset($cooperate->team_leader) ? $cooperate->team_leader : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>核税情况</label>
                            <select class="pure-input-1-2 "  name="tax" >
                                <?php
                                foreach (LACooperateModel::$arrTax as $key => $tax)
                                {
                                    ?>
                                    <option value="<?= $key ?>" <?= isset($cooperate->tax) && $cooperate->tax == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($tax) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="pure-u-md-1-2"><label>代理情况</label>
                            <input type="text" class="pure-input-2-3 submit" name="agent" id="agent" value="<?= isset($cooperate->agent) ? $cooperate->agent : ''; ?>" />
                        </div>



                        <div class="pure-u-md-1-2"><label>证件照上传</label>
                            <input type="text" class="pure-input-2-3 submit" name="id_img" id="id_img" value="<?= isset($cooperate->id_img) ? $cooperate->id_img : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>使用情况</label>
                            <input type="text" class="pure-input-2-3 submit" name="case_usage" id="case_usage" value="<?= isset($cooperate->case_usage) ? $cooperate->case_usage : ''; ?>" />
                        </div>

                        <div class="pure-u-md-1-2"><label>备注</label>
                            <input type="text" class="pure-input-2-3 submit" name="remarks" id="remarks" value="<?= isset($cooperate->remarks) ? $cooperate->remarks : ''; ?>" />
                        </div>

                    </div>
                </div>
                <h1>账户信息</h1>
                <div id="linkForm" class="pure-form pure-form-aligned">
                    <table class="pure-table" style="width:100%;">
                        <thead>
                        </thead>
                        <tbody id="link">
                        <tr class="add">
                            <td>基本户:</td>
                            <td><input type="text" name="account_basic_name" required="" data-err="请输入开户行" placeholder="开户行" value="<?= isset($cooperate->account_basic_name) ? $cooperate->account_basic_name : ''; ?>"></td>
                            <td><input type="text" name="account_basic_number" required="" data-err="请输入户账号" placeholder="户账号" value="<?= isset($cooperate->account_basic_number) ? $cooperate->account_basic_number : ''; ?>"></td>
                        </tr>
                        <tr class="add">
                            <td>一般户:</td>
                            <td><input type="text" name="account_commonly_name" required="" data-err="请输入开户行" placeholder="开户行" value="<?= isset($cooperate->account_commonly_name) ? $cooperate->account_commonly_name : ''; ?>"></td>
                            <td><input type="text" name="account_commonly_number" required="" data-err="请输入户账号" placeholder="户账号" value="<?= isset($cooperate->account_commonly_number) ? $cooperate->account_commonly_number : ''; ?>"></td>
                        </tr>
                        <tr class="add">
                            <td>募集户:</td>
                            <td><input type="text" name="account_raise_name" required="" data-err="请输入开户行" placeholder="开户行" value="<?= isset($cooperate->account_raise_name) ? $cooperate->account_raise_name : ''; ?>"></td>
                            <td><input type="text" name="account_raise_number" required="" data-err="请输入户账号" placeholder="户账号" value="<?= isset($cooperate->account_raise_number) ? $cooperate->account_raise_number : ''; ?>"></td>
                        </tr>
                        <tr class="add">
                            <td>托管户:</td>
                            <td><input type="text" name="account_trusteeship_name" required="" data-err="请输入开户行" placeholder="开户行" value="<?= isset($cooperate->account_trusteeship_name) ? $cooperate->account_trusteeship_name : ''; ?>"></td>
                            <td><input type="text" name="account_trusteeship_number" required="" data-err="请输入户账号" placeholder="户账号" value="<?= isset($cooperate->account_trusteeship_number) ? $cooperate->account_trusteeship_number : ''; ?>"></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form_action pure-form">
                <button type="submit" class="pure-button pure-button-primary" id="save">提交保存</button>
            </div>
        </form>
    </div>
</div>
<?php
/**
 * Created by PhpStorm.
 * User: rwu
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

        <form class="pure-form" saveAction="<?= Yii::app()->createUrl('product/save')?>" method="post" id="form" ">

        <div class="editor_box">
            <div class="editor_form">
                <h2>基本信息</h2>
                <div class="pure-g">
                    <input  name="pid" type="hidden" value="<?= isset($pid) ?$pid : '' ?>">
                    <input  name="ppid" type="hidden" value="<?= $ppid ?>">
                    <input type="hidden" key=''token" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" csrfInput/>

                    <div class="pure-u-1"><label>项目名称</label>
                        <input type="text" class="pure-input-1-2  submit" disabled value="<?= isset($pproduct->name) ? $pproduct->name : ''?>">
                    </div>

                    <div class="pure-u-1"><label>基金代码</label>
                        <input type="text" class="pure-input-1-2  submit" disabled value="<?= isset($pproduct->fund_code) ? $pproduct->fund_code : ''?>">
                    </div>

                    <div class="pure-u-1"><label>预计到期日</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproduct->expected_date) ? $pproduct->expected_date : ''?>">
                    </div>

                    <div class="pure-u-md-1-2"><label>收益分配方式</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset(LAPProductModel::$arrMode[$pproduct->mode]) ? LAPProductModel::$arrMode[$pproduct->mode] : ''?>">
                    </div>

                    <div class="pure-u-md-1-2"><label>项目名称</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproductDetail->project_name) ? $pproductDetail->project_name : ''?>">
                    </div>

                    <div class="pure-u-md-1-2"><label>融资方名称</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproductDetail->finance_name) ? $pproductDetail->finance_name : ''?>">
                    </div>

                    <div class="pure-u-md-1-2"><label>项目资金用途</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproductDetail->money_use) ? $pproductDetail->money_use : ''?>">
                    </div>

                    <div class="pure-u-md-1-2"><label>还款来源</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproductDetail->payment_source) ? $pproductDetail->payment_source : ''?>">
                    </div>
                </div>
            </div>
        </div>


        <div class="editor_box">
            <h1>项目信息</h1>
            <div class="editor_form">

                <div class="pure-g">

                    <div class="pure-u-md-1-2"><label>预期年化收益率</label>
                        <input type="text" class="pure-input-2-3 submit" name="expected_income_rate_E6" value="<?= isset($product->expected_income_rate_E6) ? $product->expected_income_rate_E6 : ''?>"">
                    </div>

                    <div class="pure-u-md-1-2"><label>产品总额/元</label>
                        <input type="text" class="pure-input-2-3 submit" name="total_count" value="<?= isset($product->total_count) ? $product->total_count : ''?>"">
                    </div>

                    <div class="pure-u-1"><label>实际募集/元</label>
                        <input type="text" class="pure-input-2-3 submit" name="actually_total" value="<?= isset($product->actually_total) ? $product->actually_total : ''?>"">
                    </div>

                    <div class="pure-u-1"><label>单用户限购/元</label>
                        <input type="text" class="pure-input-2-3 submit" name="per_user_by_limit" value="<?= isset($product->per_user_by_limit) ? $product->per_user_by_limit : ''?>"">
                    </div>

                    <div class="pure-u-1"><label>单笔最大金额/元</label>
                        <input type="text" class="pure-input-2-3 submit" name="max_buy" value="<?= isset($product->max_buy) ? $product->max_buy : ''?>"">
                    </div>

                    <div class="pure-u-1"><label>单笔最小金额/元</label>
                        <input type="text" class="pure-input-2-3 submit" name="min_buy" value="<?= isset($product->min_buy) ? $product->min_buy : ''?>"">
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
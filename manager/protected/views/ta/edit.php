<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/14/17
 * Time: 22:16
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

        <form class="pure-form" saveAction="<?= Yii::app()->createUrl('ta/save')?>" method="post" id="form" ">
        <div class="editor_box">
            <div class="editor_form">
                <h2>项目信息</h2>
                <div class="pure-g">
                    <input  name="ppid" type="hidden" value="<?= isset($ppid) ?$ppid : '' ?>">
                    <input  name="tid" type="hidden" value="<?= isset($tid) ?$tid : '' ?>">
                    <input  name="value_date" type="hidden" value="<?= isset($pproduct->value_date) ? $pproduct->value_date : '' ?>">
                    <input type="hidden" key=''token" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" csrfInput/>

                    <div class="pure-u-1"><label>基金代码</label>
                        <input type="text" class="pure-input-1-2  submit" disabled value="<?= isset($pproduct->fund_code) ? $pproduct->fund_code : '';?>"> *
                    </div>

                    <div class="pure-u-1"><label>基金名称</label>
                        <input type="text" class="pure-input-1-2  submit" disabled value="<?= isset($pproduct->name) ? $pproduct->name : '';?>"> *
                    </div>

                    <div class="pure-u-1"><label>产品额度</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproduct->scale)  ? $pproduct->scale  / LConstService::E4 : ' '?>"> 万元
                    </div>

                    <div class="pure-u-1"><label>收益分配方式</label>
                        <select class="pure-input-1-2 " disabled >
                            <?php
                            foreach (LAPProductModel::$arrMode as $key => $mode)
                            {
                                ?>
                                <option value="<?= $key ?>" <?= isset($pproduct->mode) && $pproduct->mode == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($mode) ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>



        <div class="editor_box">
            <h1>分配信息</h1>
            <div class="editor_form">

                <div class="pure-g">

                    <div class="pure-u-1"><label>分配期次</label>
                        <select class="pure-input-1-2 " name="term" >
                            <?php
                            foreach (LATaModel::$arrTerm as $key => $term)
                            {
                                ?>
                                <option value="<?= $key ?>" <?= isset($ta->term) && $ta->term == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($term) ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="pure-u-1"><label>实际到期日</label>
                        <input type="text" class="pure-input-2-3 submit" name="fact_end_date" datepicker="datepicker"  value="<?= isset($ta->fact_end_date) ? date('Y-m-d', $ta->fact_end_date) : ''?>">
                    </div>

                    <div class="pure-u-1"><label>预计到期日</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproduct->expected_date) ? date('Y-m-d', $pproduct->expected_date) : ''?>">
                    </div>

                    <div class="pure-u-1"><label>到期本金</label>
                        <input type="text" class="pure-input-2-3 submit" name="fact_principal" value="<?= isset($ta->fact_principal) ? $ta->fact_principal/ LConstService::E4 : ''?>"> 万元
                    </div>

                    <div class="pure-u-1"><label>预计本金</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproduct->scale) ? $pproduct->scale / LConstService::E4 : ''?>"> 万元
                    </div>

                    <div class="pure-u-1"><label>到期收益</label>
                        <input type="text" class="pure-input-2-3 submit" name="fact_income" value="<?= isset($ta->fact_income) ? $ta->fact_income/ LConstService::E4 : ''?>"> 万元
                    </div>

                    <div class="pure-u-1"><label>预计收益</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?=round((($pproduct->scale / LConstService::E4) * ($pproduct->income_rate_E6 / LConstService::E2) * ((($pproduct->expected_date - $pproduct->value_date) / 86400)) / 365), 2) / LConstService::E4?>"> 万元
                    </div>

                    <div class="pure-u-1"><label>实际收益率</label>
                        <input type="text" class="pure-input-2-3 submit" name="fact_income_rate_E6" value="<?= isset($ta->fact_income_rate_E6) ? $ta->fact_income_rate_E6 / LConstService::E4 : ''?>"> %
                    </div>

                    <div class="pure-u-1"><label>预计收益率</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($pproduct->income_rate_E6) ? $pproduct->income_rate_E6 / LConstService::E4 : ''?>"> %
                    </div>
                </div>
            </div>
        </div>

        <div class="form_action pure-form">
            <button type="submit" class="pure-button pure-button-primary" id="save">预清算</button>

            <?php
            if(isset($tid))
            {
                ?>
                <a href="<?= Yii::app()->createUrl('ta/editList?ppid='.$ppid) ?>" class="pure-button pure-button-primary">返回收益分配列表</a>
                <?php
            }
            ?>


        </div>
        </form>

    </div>
</div>
<div id="confirm_div_warp" style="display:none" >
</div>
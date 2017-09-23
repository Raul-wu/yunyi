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

        <form class="pure-form" saveAction="<?= Yii::app()->createUrl('ta/doExec')?>" method="post" id="form" ">
        <div class="editor_box">
            <div class="editor_form">
                <h2>项目信息</h2>
                <div class="pure-g">
                    <input  name="ppid" type="hidden" value="<?= isset($ppid) ?$ppid : '' ?>">
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

                    <div class="pure-u-1"><label>分配日期</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($ta->create_time)  ? $ta->create_time : ' '?>">
                    </div>

                    <div class="pure-u-1"><label>分配金额</label>
                        <input type="text" class="pure-input-2-3 submit" disabled value="<?= isset($ta->fact_income)  ? $ta->fact_income  / LConstService::E4 : ' '?>"> 万元
                    </div>

                    <div class="pure-u-1"><label>清算结果</label>
                        <?php
                        if(empty($tid))
                        {
                            ?>
                            清算前必须添加客户份额
                            <?php
                        }
                        else
                        {
                        ?>
                            <a href="<?= Yii::app()->createUrl('ta/CmbExcel?tid='.$tid) ?>" class="pure-button pure-button-primary">招商银行版</a>
                            <a href="<?= Yii::app()->createUrl('ta/SHBankExcel?tid='.$tid) ?>" class="pure-button pure-button-primary">上海银行版</a>
                        <?php
                        }
                        ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="form_action pure-form">
            <button type="submit" class="pure-button pure-button-primary" id="save" <?php if(empty($tid)){?> disabled <?php }?> >执行清算</button>
        </div>
        </form>

    </div>
</div>
<div id="confirm_div_warp" style="display:none" >
</div>
<?php
/**
 * Created by PhpStorm.
 * User: Raul
 * Date: 7/11/17
 * Time: 21:11
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

        <form class="pure-form" saveAction="<?= Yii::app()->createUrl('PProduct/save')?>" method="post" id="form" ">
            <div class="editor_box">
                <div class="editor_form">
                    <h2>基本信息</h2>
                    <div class="pure-g">
                        <input  name="ppid" type="hidden" value="<?= isset($ppid) ?$ppid : '' ?>">
                        <input type="hidden" key=''token" class="submit" name="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>" csrfInput/>

                        <div class="pure-u-1"><label>基金代码</label>
                            <input type="text" class="pure-input-1-2  submit"  name="fund_code" maxlength="255" placeholder="必填" value="<?= isset($pproduct['fund_code']) ? $pproduct['fund_code'] : '';?>"> *
                        </div>

                        <div class="pure-u-1"><label>基金名称</label>
                            <input type="text" class="pure-input-1-2  submit"  name="name" maxlength="255" placeholder="必填" value="<?= isset($pproduct['name']) ? $pproduct['name'] : '';?>"> *
                        </div>

                        <div class="pure-u-1"><label>产品结构</label>
                            <select class="pure-input-1-2 "  name="struct" >
                                <?php
                                foreach (LAPProductModel::$arrStruct as $key => $struct)
                                {
                                    ?>
                                    <option value="<?= $key ?>" <?= isset($pproduct['struct']) && $pproduct['struct'] == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($struct) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="pure-u-1"><label>产品类型</label>
                            <select class="pure-input-1-2 "  name="type" >
                                <?php
                                foreach (LAPProductModel::$arrType as $key => $type)
                                {
                                    ?>
                                    <option value="<?= $key ?>" <?= isset($pproduct['type']) && $pproduct['type'] == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($type) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="pure-u-1"><label>收益分配方式</label>
                            <select class="pure-input-1-2 "  name="mode" >
                                <?php
                                foreach (LAPProductModel::$arrMode as $key => $mode)
                                {
                                    ?>
                                    <option value="<?= $key ?>" <?= isset($pproduct['mode']) && $pproduct['mode'] == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($mode) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="pure-u-1"><label>募集规模</label>
                            <input type="text" class="pure-input-2-3 submit" name="scale" value="<?= isset($pproduct['scale'])  ? $pproduct['scale']  / LConstService::E4 : ' '?>"> 万元 *
                        </div>

                        <div class="pure-u-md-1-2"><label>剩余额度</label>
                            <input type="text" class="pure-input-2-3 submit" name="remain" disabled value="<?= isset($remain) && ($pproduct['scale'] - $remain) / LConstService::E4 > 0 ? ($pproduct['scale'] - $remain) / LConstService::E4 : 0?>"> 万元
                        </div>

                        <div class="pure-u-md-1-2"><label>收益率</label>
                            <input type="text" class="pure-input-2-3 submit" name="income_rate_E6" placeholder="必填" value="<?= isset($pproduct['income_rate_E6']) ? $pproduct['income_rate_E6'] / LConstService::E4 : ''?>">% *
                        </div>

                        <div class="pure-u-md-1-2"><label>认购费</label>
                            <input type="text" class="pure-input-2-3 submit" name="buy_rate_E6" value="<?= isset($pproduct['buy_rate_E6']) ? $pproduct['buy_rate_E6'] / LConstService::E4 : ''?>"> %
                        </div>

                        <div class="pure-u-md-1-2"><label>批次</label>
                            <input type="text" class="pure-input-2-3 submit" name="batch" value="<?= isset($pproduct['batch']) ? $pproduct['batch'] : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>成立日</label>
                            <input type="text" class="pure-input-2-3 submit" name="establish" datepicker="datepicker" value="<?= isset($pproduct['establish']) ? date('Y-m-d', $pproduct['establish']) : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>起息日</label>
                            <input type="text" class="pure-input-2-3 submit" name="value_date" placeholder="必填" datepicker="datepicker" value="<?= isset($pproduct['value_date']) ? date('Y-m-d',$pproduct['value_date']) : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>存续期限</label>
                            <input type="text" class="pure-input-2-3 submit" name="duration_data" value="<?= isset($pproduct['duration_data']) ? $pproduct['duration_data'] : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>预计到期日</label>
                            <input type="text" class="pure-input-2-3 submit" name="expected_date" placeholder="必填" datepicker="datepicker" value="<?= isset($pproduct['expected_date']) ? date('Y-m-d', $pproduct['expected_date']) : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>计息原则</label>
                            <select class="pure-input-1-2 "  name="interest_principle" >
                                <?php
                                foreach (LAPProductModel::$arrPrinciple as $key => $principle)
                                {
                                    ?>
                                    <option value="<?= $key ?>" <?= isset($pproduct['interest_principle']) && $pproduct['interest_principle'] == $key ? "selected=\"selected\"" : "" ?> ><?= CHtml::encode($principle) ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                        <div class="pure-u-md-1-2"><label>A管理费</label>
                            <input type="text" class="pure-input-2-3 submit" name="management_E6" value="<?= isset($pproduct['management_E6']) ? $pproduct['management_E6'] / LConstService::E4 : ''?>"> %
                        </div>

                        <div class="pure-u-md-1-2"><label>B托管费</label>
                            <input type="text" class="pure-input-2-3 submit" name="trusteeship_E6" value="<?= isset($pproduct['trusteeship_E6']) ? $pproduct['trusteeship_E6'] / LConstService::E4 : ''?>"> %
                        </div>

                        <div class="pure-u-md-1-2"><label>C外包费</label>
                            <input type="text" class="pure-input-2-3 submit" name="epiboly_E6" value="<?= isset($pproduct['epiboly_E6']) ? $pproduct['epiboly_E6'] / LConstService::E4 : ''?>"> %
                        </div>

                        <div class="pure-u-md-1-2"><label>D客户/销售服务费</label>
                            <input type="text" class="pure-input-2-3 submit" name="service_fees_E6" value="<?= isset($pproduct['service_fees_E6']) ? $pproduct['service_fees_E6'] / LConstService::E4 : ''?>"> %
                        </div>

                        <div class="pure-u-md-1-2"><label>E投资/财务顾问费</label>
                            <input type="text" class="pure-input-2-3 submit" name="adviser_fees_E6" value="<?= isset($pproduct['adviser_fees_E6']) ? $pproduct['adviser_fees_E6'] / LConstService::E4 : ''?>"> %
                        </div>

                        <div class="pure-u-md-1-2"><label>委贷利率</label>
                            <input type="text" class="pure-input-2-3 submit" name="lending_rate_E6" value="<?= isset($pproduct['lending_rate_E6']) ? $pproduct['lending_rate_E6'] / LConstService::E4 : ''?>"> %
                        </div>

                        <div class="pure-u-md-1-2"><label>投资期限</label>
                            <input type="text" class="pure-input-2-3 submit" name="investment_term" value="<?= isset($pproduct['investment_term']) ? $pproduct['investment_term'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>付费规则</label>
                            <div class="pure-u-3-4">
                                <textarea rows="3" type="text" style="width:600px;" name="pay_rule" ><?= isset( $pproduct['pay_rule']) ? CHtml::encode($pproduct['pay_rule']) : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="editor_box">
                <h1>项目信息</h1>
                <div class="editor_form">

                    <div class="pure-g">

                        <div class="pure-u-1"><label>融资方名称</label>
                            <input type="text" class="pure-input-2-3 submit" name="finance_name" placeholder="必填" value="<?= isset($pproduct['finance_name']) ? $pproduct['finance_name'] : ''?>"> *
                        </div>

                        <div class="pure-u-1"><label>项目名称</label>
                            <input type="text" class="pure-input-2-3 submit" name="project_name" value="<?= isset($pproduct['project_name']) ? $pproduct['project_name'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>融资方母公司</label>
                            <input type="text" class="pure-input-2-3 submit" name="parent_finance_name" value="<?= isset($pproduct['parent_finance_name']) ? $pproduct['parent_finance_name'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>项目资金用途</label>
                            <input type="text" class="pure-input-2-3 submit" name="money_use" value="<?= isset($pproduct['money_use']) ? $pproduct['money_use'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>还款来源</label>
                            <input type="text" class="pure-input-2-3 submit" name="payment_source" value="<?= isset($pproduct['payment_source']) ? $pproduct['payment_source'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>风险控制</label>
                            <input type="text" class="pure-input-2-3 submit" name="risk_control" value="<?= isset($pproduct['risk_control']) ? $pproduct['risk_control'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>项目城市</label>
                            <input type="text" class="pure-input-2-3 submit" name="project_city" value="<?= isset($pproduct['project_city']) ? $pproduct['project_city'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>项目详址</label>
                            <input type="text" class="pure-input-2-3 submit" name="project_address" value="<?= isset($pproduct['project_address']) ? $pproduct['project_address'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>项目详址 地图</label>
                            <input type="text" class="pure-input-2-3 submit" name="project_address_img" value="<?= isset($pproduct['project_address_img']) ? $pproduct['project_address_img'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>项目详址 说明</label>
                            <div class="pure-u-3-4">
                                <textarea  rows="3" type="text" style="width:600px;"  name="project_address_explain" > <?=isset($pproduct['project_address_explain']) ? CHtml::encode($pproduct['project_address_explain']) : ''?></textarea>
                            </div>
                        </div>

                        <div class="pure-u-1"><label>项目简述</label>
                            <div class="pure-u-3-4">
                                <textarea rows="3" type="text" style="width:600px;"  name="project_summary" > <?= isset($pproduct['project_summary']) ? CHtml::encode($pproduct['project_summary']) : ''?></textarea>
                            </div>
                        </div>

                        <div class="pure-u-1"><label>项目详述</label>
                            <div class="pure-u-3-4">
                                <textarea rows="3" type="text" style="width:600px;"  name="project_detail" > <?= isset($pproduct['project_detail']) ?  CHtml::encode($pproduct['project_detail']) : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="editor_box">
                <h1>项目信息</h1>
                <div class="editor_form">

                    <div class="pure-g">

                        <div class="pure-u-md-1-2"><label>管理人</label>
                            <input type="text" class="pure-input-2-3 submit" name="manager" placeholder="必填" value="<?= isset($pproduct['manager']) ? $pproduct['manager'] : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>团队负责人</label>
                            <input type="text" class="pure-input-2-3 submit" name="team_leader" placeholder="必填" value="<?= isset($pproduct['team_leader']) ? $pproduct['team_leader'] : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>项目经理</label>
                            <input type="text" class="pure-input-2-3 submit" name="project_manager" placeholder="必填" value="<?= isset($pproduct['project_manager']) ? $pproduct['project_manager'] : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>托管人</label>
                            <input type="text" class="pure-input-2-3 submit" name="trustee" placeholder="必填" value="<?= isset($pproduct['trustee']) ? $pproduct['trustee'] : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>项目类型</label>
                            <input type="text" class="pure-input-2-3 submit" name="project_type" placeholder="必填" value="<?= isset($pproduct['project_type']) ? $pproduct['project_type'] : ''?>"> *
                        </div>

                        <div class="pure-u-md-1-2"><label>部门</label>
                            <input type="text" class="pure-input-2-3 submit" name="department" placeholder="必填" value="<?= isset($pproduct['department']) ? $pproduct['department'] : ''?>"> *
                        </div>

                        <div class="pure-u-1"><label>风险等级</label>
                            <input type="text" class="pure-input-2-3 submit" name="risk_level" value="<?= isset($pproduct['risk_level']) ? $pproduct['risk_level'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>法律结构</label>
                            <input type="text" class="pure-input-2-3 submit" name="legal_structure" value="<?= isset($pproduct['legal_structure']) ? $pproduct['legal_structure'] : ''?>">
                        </div>

                        <div class="pure-u-1"><label>发行机构</label>
                            <input type="text" class="pure-input-2-3 submit" name="publishing_organization" value="<?= isset($pproduct['publishing_organization']) ? $pproduct['publishing_organization'] : ''?>">
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
<?php
/**
 * Created by PhpStorm.
 * User: Raul
 * Date: 7/10/17
 * Time: 21:24
 */

Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);

//Yii::app()->clientScript->registerScript("url", 'window.url='.json_encode($url).';', CClientScript::POS_END);

Yii::app()->clientScript->registerScript("addSpvPermission", 'window.addSpvPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2006102).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("changeStateSpvPermission", 'window.changeStateSpvPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2006101).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("listSpvPermission", 'window.listSpvPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 9999).'";', CClientScript::POS_END);
?>

<div id="main">
    <div class="quick_action">
        <div class="action_mod">
            <form class="pure-form" action="<?= Yii::app()->createUrl("/quotient/list/")?>">
                <input type="hidden" id="tkName" tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>">
                <div class="pure-g">
                    <div class="pure-u-2-3">
                        <input type="text"  placeholder="子产品名称" class="pure-input-1-1"  value="<?= isset($name) ? $name : '' ?>" name="name" id="name" />
                        <input type="text"  placeholder="基金名称" class="pure-input-1-1"  value="<?= isset($fund_name) ? $fund_name : '' ?>" name="fund_name" id="fund_name" />
                        <button type="submit" class="pure-button pure-button-primary">筛选</button>
                        <button type="button" id="reset" class="pure-button">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="quick_action fix_panel">
        <div class="action_mod">
        </div>
    </div>

    <div class="content">
        <div class="table_mod">
            <table class="pure-table">
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
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>产品名称</th>
                    <th>投资人姓名</th>
                    <th>交易金额（万元）</th>
                    <th>投资类型</th>
                    <th>证件类别</th>
                    <th>证件号码</th>
                    <th>经办人姓名</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(!empty($quotients))
                {
                    foreach ($quotients as $key => $quotient)
                    {
                        ?>
                        <tr class="<?= $key % 2 ? "" : "pure-table-odd" ?> pure-table-tr">
                            <td><input type="checkbox" value="<?= $quotient['qid'] ?>" class="check" data-id="<?= $quotient['qid'] ?>"/></td>
                            <td class="tc"><?= $quotient['product']['name'] ?></td>
                            <td class="tc"><?= $quotient['name'] ?></td>
                            <td class="tc"><?= $quotient['amount'] ?></td>
                            <td class="tc"><?= isset(LAQuotientModel::$arrType[$quotient['type']]) ? LAQuotientModel::$arrType[$quotient['type']] : '' ?></td>
                            <td class="tc"><?= isset(LAQuotientModel::$arrIdType[$quotient['id_type']]) ? LAQuotientModel::$arrIdType[$quotient['id_type']] : '' ?></td>
                            <td class="tc"><?= $quotient['id_content'] ?></td>
                            <td class="tc"><?= $quotient['handler_name'] ?></td>
                            <td class="tc"><?= isset(LAQuotientModel::$arrStatus[$quotient['status']]) ? LAQuotientModel::$arrStatus[$quotient['status']] : '' ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="screening_result fl">
            <div class="fl">筛选到<?= $count ?>个产品</div>
        </div>
        <?= $pageBar ?>
    </div>
</div>
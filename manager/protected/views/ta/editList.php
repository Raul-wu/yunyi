<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/17/17
 * Time: 21:27
 */
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);

Yii::app()->clientScript->registerScript("edit", 'window.edit="'.Yii::app()->createUrl('ta/edit').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("deleteTa", 'window.deleteTa="'.Yii::app()->createUrl('ta/delete').'";', CClientScript::POS_END);
?>

<div id="main">

    <div class="quick_action fix_panel">
        <div class="action_mod"  >
            <a type="submit" id="buttonHolder"></a>
        </div>
    </div>

    <div class="content">
        <input type="hidden" id="tkName" tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>">
        <div class="table_mod">

            <table class="pure-table">
                <colgroup>
                    <col class="w_60" />
                    <col class="w_120" />
                    <col class="w_120" />
                    <col class="w_120" />
                    <col class="w_120" />
                    <col class="w_120" />
                    <col class="w_120" />
                    <col class="w_120" />
                </colgroup>
                <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>基金代码</th>
                    <th>基金名称</th>
                    <th>分配期次</th>
                    <th>实际到期日</th>
                    <th>到期本金/万元</th>
                    <th>到期收益/万元</th>
                    <th>实际收益率</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($tas as $key => $ta)
                {
                    ?>
                    <tr class="<?= $key % 2 ? "" : "pure-table-odd"?>  pure-table-tr" id="tr<?= $ta->tid ?>">
                        <td><input class="check"  type="checkbox" data-id="<?= $ta->tid ?>"></td>
                        <td><?= $ta['pproduct']['fund_code'] ?></td>
                        <td><?= $ta['pproduct']['name'] ?></td>
                        <td><?= isset(LATaModel::$arrTerm[$ta->term]) ? LATaModel::$arrTerm[$ta->term] : ''?></td>
                        <td><?= date('Y-m-d', $ta->fact_end_date) ?></td>
                        <td><?= $ta->fact_principal / LConstService::E4 ?></td>
                        <td><?= $ta->fact_income / LConstService::E4 ?></td>
                        <td><?= $ta->fact_income_rate_E6 / LConstService::E4 ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <div>
            <div class="">
                <div class="fl">共筛选到<?= $count ?>个产品</div>

                <div class="loadHolder"></div>

            </div>

            <?= $pageBar ?>
        </div>
    </div>
</div>
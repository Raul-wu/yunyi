<?php
/**
 * Created by PhpStorm.
 * User: Raul
 * Date: 7/18/17
 * Time: 09:09
 */
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);

Yii::app()->clientScript->registerScript("detail", 'window.detail="'.Yii::app()->createUrl('history/detail').'";', CClientScript::POS_END);

?>

<div id="main">
    <div class="quick_action">
        <div class="action_mod">
            <form class="pure-form">
                <div class="pure-g">
                    <div class="pure-u-2-3">
                        <input type="text"   placeholder="基金代码" class="pure-input-1-1" id="fund_code" value="<?=isset($fund_code) ? $fund_code : ''?>" name="fund_code" >
                        <button type="submit" class="pure-button pure-button-primary">筛选</button>
                        <button type="button" id="reset" class="pure-button">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                    <th>收益类型</th>
                    <th>募集规模（万元）</th>
                    <th>预计到期</th>
                    <th>分配方式</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($pproducts as $key => $pproduct)
                {
                    ?>
                    <tr class="<?= $key % 2 ? "" : "pure-table-odd"?>  pure-table-tr" id="tr<?= $pproduct['ppid'] ?>">
                        <td><input class="check"  type="checkbox" data-id="<?= $pproduct['ppid'] ?>"></td>
                        <td><?= $pproduct['fund_code'] ?></td>
                        <td><?= $pproduct['name'] ?></td>
                        <td><?= isset(LAPProductModel::$arrType[$pproduct['type']]) ? LAPProductModel::$arrType[$pproduct['type']] : ''?></td>
                        <td><?= $pproduct['scale'] / LConstService::E4 ?></td>
                        <td><?= date('Y-m-d',$pproduct['expected_date']) ?></td>
                        <td><?= isset(LAPProductModel::$arrMode[$pproduct['mode']]) ? LAPProductModel::$arrMode[$pproduct['mode']] : '' ?></td>
                        <td><?= isset(LAPProductModel::$arrStatus[$pproduct['status']]) ? LAPProductModel::$arrStatus[$pproduct['status']] : '' ?></td>
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
<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 8/7/17
 * Time: 22:37
 */
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);

?>

<div id="main">
    <div class="quick_action">
        <div class="action_mod">
            <form class="pure-form">
                <div class="pure-g">
                    <div class="pure-u-2-3">
                        <select  name="status">
                            <option value="0">不限产品状态</option>
                            <?php
                            foreach (LAPProductModel::$arrStatus as $key => $value)
                            {
                                ?>
                                <option value="<?= $key ?>" <?= $status == $key ? "selected=\"selected\"" : "" ?> ><?= $value ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <button type="submit" class="pure-button pure-button-primary">筛选</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="content">
        <input type="hidden" id="tkName" tkName="<?= Yii::app()->request->csrfTokenName ?>" value="<?= Yii::app()->request->csrfToken ?>">
        <div class="table_mod">

            <table class="pure-table">
                <colgroup>
                    <col class="w_60" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                    <col class="w_80" />
                </colgroup>
                <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>基金ID</th>
                    <th>基金代码</th>
                    <th>基金名称</th>
                    <th>额度(元)</th>
                    <th>预期收益</th>
                    <th>起息日</th>
                    <th>到期日</th>
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
                        <td><?= $pproduct['ppid'] ?></td>
                        <td><?= $pproduct['fund_code'] ?></td>
                        <td><?= $pproduct['name'] ?></td>
                        <td><?= $pproduct['scale']?></td>
                        <td><?= $pproduct['income_rate_E6'] / LConstService::E4  ?></td>
                        <td><?= date('Y-m-d',$pproduct['value_date']) ?></td>
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
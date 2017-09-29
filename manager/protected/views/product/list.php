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

//Yii::app()->clientScript->registerScript("durationUrl", 'window.durationUrl="'.Yii::app()->createUrl('/PProduct/duration/').'";', CClientScript::POS_END);
//Yii::app()->clientScript->registerScript("subCreate", 'window.subCreate="'.Yii::app()->createUrl("p/new").'";', CClientScript::POS_END);
//Yii::app()->clientScript->registerScript("subShow", 'window.subShow="'.Yii::app()->createUrl('p/list').'";', CClientScript::POS_END);
//Yii::app()->clientScript->registerScript("url", 'window.url="'.Yii::app()->createUrl('PProduct/edit').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("editProduct", 'window.editProduct="'.Yii::app()->createUrl('product/edit').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("quotient", 'window.quotient="'.Yii::app()->createUrl('quotient/add').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("quotientList", 'window.quotientList="'.Yii::app()->createUrl('quotient/list').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("delProduct", 'window.delProduct="'.Yii::app()->createUrl('product/delete').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("addQuotient", 'window.addQuotient="'.Yii::app()->createUrl('quotient/addOne').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("checkProductEstablish", 'window.checkProductEstablish="'.Yii::app()->createUrl('product/checkProductIsEstablish').'";', CClientScript::POS_END);

//列表页按钮权限
//Yii::app()->clientScript->registerScript("delPProductPermission", 'window.delPProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001107).'";', CClientScript::POS_END);
//Yii::app()->clientScript->registerScript("listProductPermission", 'window.listProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001106).'";', CClientScript::POS_END);
//Yii::app()->clientScript->registerScript("DurPProductPermission", 'window.DurPProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001110).'";', CClientScript::POS_END);
//Yii::app()->clientScript->registerScript("prePProductPermission", 'window.prePProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001105).'";', CClientScript::POS_END);
//Yii::app()->clientScript->registerScript("createProductPermission", 'window.createProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001103).'";', CClientScript::POS_END);
//Yii::app()->clientScript->registerScript("copyPProductPermission", 'window.copyPProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001102).'";', CClientScript::POS_END);
?>

<div id="main">
    <div class="quick_action">
        <div class="action_mod">
            <form class="pure-form">
                <div class="pure-g">
                    <div class="pure-u-2-3">
                        <input type="text"   placeholder="基金代码" class="pure-input-1-1"  value="<?=isset($fund_code) ? $fund_code : ''?>" name="fund_code" id="fund_code" >
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
                    <col class="w_120" />
                    <col class="w_60" />
                </colgroup>
                <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>产品ID</th>
                    <th>基金代码</th>
                    <th>产品名称</th>
                    <th>额度(万元)</th>
                    <th>预期收益率</th>
                    <th>起息日</th>
                    <th>到期日</th>
                    <th>分配方式</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($products as $key => $product)
                {
                    ?>
                    <tr class="<?= $key % 2 ? "" : "pure-table-odd"?>  pure-table-tr" id="tr<?= $product['pid'] ?>">
                        <td><input class="check"  type="checkbox" data-id="<?= $product['pid'] ?>"></td>
                        <td><?= $product['pid'] ?></td>
                        <td><?= $product['pproduct']['fund_code'] ?></td>
                        <td><?= $product['pproduct']['name'] ?></td>
                        <td><?= $product['total_count'] / LConstService::E4 ?></td>
                        <td><?= $product['expected_income_rate_E6'] / LConstService::E4 ?></td>
                        <td><?= date('Y-m-d',$product['pproduct']['value_date']) ?></td>
                        <td><?= date('Y-m-d',$product['pproduct']['expected_date']) ?></td>
                        <td><?= isset(LAPProductModel::$arrMode[$product['pproduct']['mode']]) ? LAPProductModel::$arrMode[$product['pproduct']['mode']] : '' ?></td>
                        <td><?= isset(LAProductModel::$arrStatus[$product['status']]) ? LAProductModel::$arrStatus[$product['status']] : '' ?></td>
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
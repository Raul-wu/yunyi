<?php
/**
 * Created by PhpStorm.
 * User: rwu
 * Date: 7/11/17
 * Time: 20:52
 */
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/skins/black.css?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/js/lib/art_dialog/jquery.artDialog.source.js?v=" . STATIC_VER);
Yii::app()->clientScript->registerCssFile("/assets/{$this->assetsDir}/css/tables.css?v=" . STATIC_VER);

Yii::app()->clientScript->registerScript("durationUrl", 'window.durationUrl="'.Yii::app()->createUrl('/PProduct/duration/').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("subCreate", 'window.subCreate="'.Yii::app()->createUrl("p/new").'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("url", 'window.url="'.Yii::app()->createUrl('PProduct/edit').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("deletePProduct", 'window.deletePProduct="'.Yii::app()->createUrl('PProduct/delete').'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("subCreate", 'window.subCreate="'.Yii::app()->createUrl("product/add").'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("subShow", 'window.subShow="'.Yii::app()->createUrl('product/list').'";', CClientScript::POS_END);

//列表页按钮权限
Yii::app()->clientScript->registerScript("delPProductPermission", 'window.delPProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001107).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("listProductPermission", 'window.listProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001106).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("DurPProductPermission", 'window.DurPProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001110).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("prePProductPermission", 'window.prePProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001105).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("createProductPermission", 'window.createProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001103).'";', CClientScript::POS_END);
Yii::app()->clientScript->registerScript("copyPProductPermission", 'window.copyPProductPermission="'.LAPermissionService::selectMenuPermission($this->menuId, 2001102).'";', CClientScript::POS_END);
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
            <a id="btnPh" href="<?php echo Yii::app()->createUrl("PProduct/add") ?>" class="pure-button pure-button-primary " style="">创建母产品</a>

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
                </colgroup>
                <thead>
                <tr>
                    <th><input type="checkbox" id="selectAll"></th>
                    <th>基金代码</th>
                    <th>项目名称</th>
                    <th>收益类型</th>
                    <th>募集规模（万元）</th>
                    <th>预计到期</th>
                    <th>分配方式</th>
                    <th>状态</th>
                    <th>操作</th>
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
                        <td><?= $pproduct['scale'] ?></td>
                        <td><?= date('Y-m-d',$pproduct['expected_date']) ?></td>
                        <td><?= isset(LAPProductModel::$arrMode[$pproduct['mode']]) ? LAPProductModel::$arrMode[$pproduct['mode']] : '' ?></td>
                        <td><?= isset(LAPProductModel::$arrStatus[$pproduct['status']]) ? LAPProductModel::$arrStatus[$pproduct['status']] : '' ?></td>
                        <td class="tc">
                            <a href="<?= Yii::app()->createUrl('PProduct/edit/', array('ppid' => $pproduct['ppid'])) ?>">编辑</a>
                        </td>
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